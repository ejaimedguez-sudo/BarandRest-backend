import { Router } from "express";
import dayjs from "dayjs";
import { authRequired, withRoles } from "../middlewares/auth.js";
import { Invoice, Order, OrderItem, Payment, Ticket } from "../models.js";
import { processPayment } from "../providers/payment-provider.js";
import { stampInvoice } from "../providers/cfdi-provider.js";
import { publishEvent } from "../realtime/event-bus.js";

const router = Router();

function calculateTotals(orderItems) {
  const subtotal = orderItems.reduce((acc, row) => acc + Number(row.qty) * Number(row.unitPrice), 0);
  const tax = subtotal * 0.16;
  return { subtotal, tax, total: subtotal + tax };
}

router.post("/tickets/:orderId", authRequired, withRoles("cajero", "administrador", "gerente"), async (req, res) => {
  try {
    const existing = await Ticket.findOne({ where: { OrderId: req.params.orderId } });
    if (existing) {
      return res.json(existing);
    }

    const order = await Order.findByPk(req.params.orderId, { include: [OrderItem] });
    if (!order) return res.status(404).json({ message: "Orden no encontrada" });

    const totals = calculateTotals(order.OrderItems || []);
    const ticket = await Ticket.create({
      OrderId: order.id,
      folio: `TCK-${dayjs().format("YYYYMMDD-HHmmss")}-${order.id}`,
      ...totals
    });

    publishEvent("ticket.created", { ticketId: ticket.id, orderId: order.id, total: Number(ticket.total) });

    return res.status(201).json(ticket);
  } catch (error) {
    return res.status(400).json({ message: "No se pudo generar ticket", detail: error.message });
  }
});

router.post("/payments/:ticketId", authRequired, withRoles("cajero", "administrador", "gerente"), async (req, res) => {
  try {
    const providerResult = await processPayment({
      amount: Number(req.body.amount),
      method: req.body.method,
      reference: `TICKET-${req.params.ticketId}-${Date.now()}`
    });

    const row = await Payment.create({
      TicketId: req.params.ticketId,
      method: req.body.method,
      amount: req.body.amount,
      externalRef: req.body.externalRef || providerResult.externalRef || null,
      status: providerResult.approved ? "approved" : req.body.status || "pending"
    });

    if (row.status === "approved") {
      const ticket = await Ticket.findByPk(req.params.ticketId);
      const paidRows = await Payment.findAll({ where: { TicketId: req.params.ticketId, status: "approved" } });
      const paidTotal = paidRows.reduce((acc, payment) => acc + Number(payment.amount || 0), 0);

      if (ticket && paidTotal >= Number(ticket.total)) {
        const order = await Order.findByPk(ticket.OrderId);
        if (order) {
          order.status = "paid";
          await order.save();
          publishEvent("order.paid", { orderId: order.id, ticketId: ticket.id, paidTotal });
        }
      }
    }

    publishEvent("payment.created", {
      paymentId: row.id,
      ticketId: Number(req.params.ticketId),
      amount: Number(row.amount),
      status: row.status,
      provider: providerResult.provider
    });

    return res.status(201).json(row);
  } catch (error) {
    return res.status(400).json({ message: "No se pudo registrar pago", detail: error.message });
  }
});

router.post("/invoices/:ticketId", authRequired, withRoles("cajero", "administrador", "gerente"), async (req, res) => {
  try {
    const existing = await Invoice.findOne({ where: { TicketId: req.params.ticketId } });
    if (existing) {
      return res.json(existing);
    }

    const ticket = await Ticket.findByPk(req.params.ticketId);
    if (!ticket) return res.status(404).json({ message: "Ticket no encontrado" });

    const stamped = await stampInvoice({
      ticket,
      fiscalData: {
        fiscalName: req.body.fiscalName,
        rfc: req.body.rfc,
        email: req.body.email
      }
    });

    const invoice = await Invoice.create({
      TicketId: ticket.id,
      fiscalName: req.body.fiscalName,
      rfc: req.body.rfc,
      email: req.body.email,
      uuidProvider: stamped.uuid
    });

    publishEvent("invoice.created", {
      invoiceId: invoice.id,
      ticketId: ticket.id,
      uuid: invoice.uuidProvider,
      provider: stamped.provider
    });

    return res.status(201).json(invoice);
  } catch (error) {
    return res.status(400).json({ message: "No se pudo generar factura", detail: error.message });
  }
});

export default router;
