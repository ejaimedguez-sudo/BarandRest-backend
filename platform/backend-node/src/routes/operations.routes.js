import { Router } from "express";
import { Order, OrderItem, RestaurantTable, User, MenuItem } from "../models.js";
import { authRequired, withRoles } from "../middlewares/auth.js";

const router = Router();

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
  const source = req.body.source || (req.user.Role.name === "cajero" ? "cashier" : "waiter");
  const order = await Order.create({
    source,
    RestaurantTableId: req.body.tableId,
    waiterId: req.body.waiterId || req.user.id,
    cashierId: req.user.Role.name === "cajero" ? req.user.id : null,
    notes: req.body.notes || null
  });

  const items = (req.body.items || []).map((item) => ({
    OrderId: order.id,
    MenuItemId: item.menuItemId,
    qty: item.qty,
    unitPrice: item.unitPrice
  }));

  await OrderItem.bulkCreate(items);
  return res.status(201).json(await Order.findByPk(order.id, { include: [{ model: OrderItem, include: [MenuItem] }] }));
});

router.post("/orders/guest", async (req, res) => {
  const order = await Order.create({
    source: req.body.useWaiter ? "waiter" : "qr",
    RestaurantTableId: req.body.tableId,
    notes: req.body.notes || "Orden solicitada desde QR"
  });

  const items = (req.body.items || []).map((item) => ({
    OrderId: order.id,
    MenuItemId: item.menuItemId,
    qty: item.qty,
    unitPrice: item.unitPrice
  }));

  await OrderItem.bulkCreate(items);
  return res.status(201).json({ message: "Comanda creada", orderId: order.id });
});

router.patch("/orders/:id/status", authRequired, withRoles("mesero", "jefe_barra", "jefe_cocina", "cajero", "gerente"), async (req, res) => {
  const order = await Order.findByPk(req.params.id);
  if (!order) return res.status(404).json({ message: "Orden no encontrada" });
  order.status = req.body.status;
  await order.save();
  return res.json(order);
});

router.get("/orders", authRequired, async (_req, res) => {
  const rows = await Order.findAll({ include: [OrderItem, RestaurantTable] });
  return res.json(rows);
});

export default router;
