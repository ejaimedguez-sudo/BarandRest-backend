import { Router } from "express";
import { AccountsPayable, Ingredient, InventoryMovement, PurchaseOrder, Supplier } from "../models.js";
import { authRequired, withRoles } from "../middlewares/auth.js";

const router = Router();

router.post("/suppliers", authRequired, withRoles("administrador", "gerente"), async (req, res) => {
  const row = await Supplier.create(req.body);
  return res.status(201).json(row);
});

router.get("/suppliers", authRequired, async (_req, res) => {
  return res.json(await Supplier.findAll());
});

router.post("/movements", authRequired, withRoles("administrador", "jefe_barra", "jefe_cocina", "gerente"), async (req, res) => {
  const ingredient = await Ingredient.findByPk(req.body.ingredientId);
  if (!ingredient) return res.status(404).json({ message: "Ingrediente no encontrado" });

  const qty = Number(req.body.qty || 0);
  if (req.body.type === "in") ingredient.stockQty = Number(ingredient.stockQty) + qty;
  if (req.body.type === "out") ingredient.stockQty = Number(ingredient.stockQty) - qty;
  if (req.body.type === "adjustment") ingredient.stockQty = qty;

  await ingredient.save();
  const movement = await InventoryMovement.create({
    IngredientId: ingredient.id,
    type: req.body.type,
    qty,
    reason: req.body.reason,
    performedById: req.user.id
  });

  return res.status(201).json({ movement, stockQty: ingredient.stockQty });
});

router.post("/purchase-orders", authRequired, withRoles("administrador", "gerente"), async (req, res) => {
  const row = await PurchaseOrder.create(req.body);
  return res.status(201).json(row);
});

router.post("/accounts-payable", authRequired, withRoles("administrador", "gerente"), async (req, res) => {
  const row = await AccountsPayable.create(req.body);
  return res.status(201).json(row);
});

router.get("/accounts-payable", authRequired, withRoles("administrador", "gerente", "cajero"), async (_req, res) => {
  return res.json(await AccountsPayable.findAll({ include: Supplier }));
});

export default router;
