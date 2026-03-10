import { Router } from "express";
import QRCode from "qrcode";
import { MenuItem, Category, RestaurantTable } from "../models.js";
import { env } from "../config/env.js";
import { authRequired, withRoles } from "../middlewares/auth.js";

const router = Router();

router.get("/public", async (_req, res) => {
  const data = await MenuItem.findAll({ where: { active: true }, include: Category });
  return res.json(data);
});

router.get("/public/table/:tableId/qr", async (req, res) => {
  const table = await RestaurantTable.findByPk(req.params.tableId);
  if (!table) return res.status(404).json({ message: "Mesa no encontrada" });

  const menuUrl = `${env.clientBaseUrl}/menu?tableId=${table.id}`;
  const qr = await QRCode.toDataURL(menuUrl);
  return res.json({ table: table.tableNumber, menuUrl, qr });
});

router.post("/items", authRequired, withRoles("administrador", "jefe_barra", "jefe_cocina"), async (req, res) => {
  const item = await MenuItem.create(req.body);
  return res.status(201).json(item);
});

export default router;
