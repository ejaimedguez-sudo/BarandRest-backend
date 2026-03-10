import { Router } from "express";
import dayjs from "dayjs";
import { authRequired, withRoles } from "../middlewares/auth.js";
import { Invoice, Order, OrderItem, Payment, Ticket } from "../models.js";

const router = Router();

function calculateTotals(orderItems) {
  const subtotal = orderItems.reduce((acc, row) => acc + Number(row.qty) * Number(row.unitPrice), 0);
  const tax = subtotal * 0.16;
  return { subtotal, tax, total: subtotal + tax };
}

router.post("/tickets/:orderId", authRequired, withRoles("cajero", "administrador", "gerente"), async (req, res) => {
  try {
    const order = await Order.findByPk(req.params.orderId, { include: [OrderItem] });
    if (!order) return res.status(404).json({ message: "Orden no encontrada" });

    const totals = calculateTotals(order.OrderItems || []);
    const ticket = await Ticket.create({
      OrderId: order.id,
      folio: `TCK-${dayjs().format("YYYYMMDD-HHmmss")}-${order.id}`,
      ...totals
    });

    return res.status(201).json(ticket);
  } catch (error) {
    return res.status(400).json({ message: "No se pudo generar ticket", detail: error.message });
  }
});

router.post("/payments/:ticketId", authRequired, withRoles("cajero", "administrador", "gerente"), async (req, res) => {
  try {
    const row = await Payment.create({
      TicketId: req.params.ticketId,
      method: req.body.method,
      amount: req.body.amount,
      externalRef: req.body.externalRef || null,
      status: req.body.status || "approved"
    });
    return res.status(201).json(row);
  } catch (error) {
    return res.status(400).json({ message: "No se pudo registrar pago", detail: error.message });
  }
});

router.post("/invoices/:ticketId", authRequired, withRoles("cajero", "administrador", "gerente"), async (req, res) => {
  try {
    const ticket = await Ticket.findByPk(req.params.ticketId);
    if (!ticket) return res.status(404).json({ message: "Ticket no encontrado" });

    // Aqui se conectaria la API timbradora/facturacion.
    const invoice = await Invoice.create({
      TicketId: ticket.id,
      fiscalName: req.body.fiscalName,
      rfc: req.body.rfc,
      email: req.body.email,
      uuidProvider: `FAKE-${ticket.folio}`
    });

    return res.status(201).json(invoice);
  } catch (error) {
    return res.status(400).json({ message: "No se pudo generar factura", detail: error.message });
  }
});

export default router;
