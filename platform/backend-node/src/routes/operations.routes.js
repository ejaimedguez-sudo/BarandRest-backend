import { Router } from "express";
import { z } from "zod";
import { sequelize } from "../config/db.js";
import { consumeInventoryForOrderItem } from "../services/order-workflow.js";
import { publishEvent } from "../realtime/event-bus.js";
import { Order, OrderItem, RestaurantTable, User, MenuItem } from "../models.js";
import { authRequired, withRoles } from "../middlewares/auth.js";

const router = Router();

const orderItemSchema = z.object({
  menuItemId: z.number().int().positive(),
  qty: z.number().positive(),
  unitPrice: z.number().positive()
});

const createOrderSchema = z.object({
  source: z.enum(["qr", "waiter", "cashier"]).optional(),
  tableId: z.number().int().positive(),
  waiterId: z.number().int().positive().optional(),
  notes: z.string().max(500).optional(),
  items: z.array(orderItemSchema).min(1)
});

async function createOrderAndItems({ payload, user, sourceOverride }) {
  return sequelize.transaction(async (transaction) => {
    const source = sourceOverride || payload.source || (user?.Role?.name === "cajero" ? "cashier" : "waiter");
    const order = await Order.create(
      {
        source,
        RestaurantTableId: payload.tableId,
        waiterId: payload.waiterId || user?.id || null,
        cashierId: user?.Role?.name === "cajero" ? user.id : null,
        notes: payload.notes || null
      },
      { transaction }
    );

    const consumption = [];
    for (const item of payload.items) {
      await OrderItem.create(
        {
          OrderId: order.id,
          MenuItemId: item.menuItemId,
          qty: item.qty,
          unitPrice: item.unitPrice
        },
        { transaction }
      );

      const stockResult = await consumeInventoryForOrderItem({
        menuItemId: item.menuItemId,
        orderQty: item.qty,
        userId: user?.id || null,
        transaction
      });
      if (stockResult.consumed) {
        consumption.push({ menuItemId: item.menuItemId, ingredients: stockResult.details });
      }
    }

    const hydrated = await Order.findByPk(order.id, {
      include: [{ model: OrderItem, include: [MenuItem] }],
      transaction
    });

    return { order: hydrated, consumption };
  });
}

router.post("/tables", authRequired, withRoles("administrador", "gerente"), async (req, res) => {
  const row = await RestaurantTable.create(req.body);
  return res.status(201).json(row);
});

router.patch("/tables/:id/assign-waiter", authRequired, withRoles("administrador", "gerente"), async (req, res) => {
  const table = await RestaurantTable.findByPk(req.params.id);
  if (!table) return res.status(404).json({ message: "Mesa no encontrada" });

  const waiter = await User.findByPk(req.body.waiterId);
  if (!waiter) return res.status(404).json({ message: "Mesero no encontrado" });

  table.assignedWaiterId = waiter.id;
  await table.save();
  return res.json(table);
});

router.post("/orders", authRequired, withRoles("mesero", "cajero", "administrador", "gerente"), async (req, res) => {
  const parsed = createOrderSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json({ message: "Payload invalido", errors: parsed.error.issues });
  }

  const result = await createOrderAndItems({ payload: parsed.data, user: req.user });
  publishEvent("order.created", {
    orderId: result.order.id,
    source: result.order.source,
    tableId: result.order.RestaurantTableId,
    userId: req.user.id
  });
  return res.status(201).json(result);
});

router.post("/orders/guest", async (req, res) => {
  const guestSchema = z.object({
    tableId: z.number().int().positive(),
    useWaiter: z.boolean().optional(),
    notes: z.string().max(500).optional(),
    items: z.array(orderItemSchema).min(1)
  });
  const parsed = guestSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json({ message: "Payload invalido", errors: parsed.error.issues });
  }

  const result = await createOrderAndItems({
    payload: parsed.data,
    user: null,
    sourceOverride: parsed.data.useWaiter ? "waiter" : "qr"
  });

  publishEvent("order.created", {
    orderId: result.order.id,
    source: result.order.source,
    tableId: result.order.RestaurantTableId,
    userId: null
  });

  return res.status(201).json({ message: "Comanda creada", orderId: result.order.id, consumption: result.consumption });
});

router.post(
  "/orders/:id/add-items",
  authRequired,
  withRoles("cajero", "administrador", "gerente"),
  async (req, res) => {
    const parsed = z.object({ items: z.array(orderItemSchema).min(1) }).safeParse(req.body);
    if (!parsed.success) {
      return res.status(400).json({ message: "Payload invalido", errors: parsed.error.issues });
    }

    const order = await Order.findByPk(req.params.id);
    if (!order) return res.status(404).json({ message: "Orden no encontrada" });

    const consumption = [];
    await sequelize.transaction(async (transaction) => {
      for (const item of parsed.data.items) {
        await OrderItem.create(
          {
            OrderId: order.id,
            MenuItemId: item.menuItemId,
            qty: item.qty,
            unitPrice: item.unitPrice
          },
          { transaction }
        );

        const stockResult = await consumeInventoryForOrderItem({
          menuItemId: item.menuItemId,
          orderQty: item.qty,
          userId: req.user.id,
          transaction
        });
        if (stockResult.consumed) {
          consumption.push({ menuItemId: item.menuItemId, ingredients: stockResult.details });
        }
      }

      order.cashierId = req.user.id;
      await order.save({ transaction });
    });

    const hydrated = await Order.findByPk(order.id, { include: [{ model: OrderItem, include: [MenuItem] }] });
    publishEvent("order.items_added", {
      orderId: hydrated.id,
      addedByUserId: req.user.id,
      itemsCount: parsed.data.items.length
    });
    return res.json({ message: "Items agregados", order: hydrated, consumption });
  }
);

router.get("/tables", authRequired, async (_req, res) => {
  const rows = await RestaurantTable.findAll({ include: [{ model: User, as: "assignedWaiter", attributes: ["id", "fullName"] }] });
  return res.json(rows);
});

router.patch("/orders/:id/status", authRequired, withRoles("mesero", "jefe_barra", "jefe_cocina", "cajero", "gerente"), async (req, res) => {
  const order = await Order.findByPk(req.params.id);
  if (!order) return res.status(404).json({ message: "Orden no encontrada" });
  order.status = req.body.status;
  await order.save();
  publishEvent("order.status_changed", { orderId: order.id, status: order.status, byUserId: req.user.id });
  return res.json(order);
});

router.get("/orders", authRequired, async (_req, res) => {
  const rows = await Order.findAll({ include: [OrderItem, RestaurantTable] });
  return res.json(rows);
});

export default router;
