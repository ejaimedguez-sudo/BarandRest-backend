import { Router } from "express";
import { Ingredient, Recipe, RecipeIngredient, MenuItem } from "../models.js";
import { authRequired, withRoles } from "../middlewares/auth.js";

const router = Router();

router.post("/ingredients", authRequired, withRoles("administrador", "jefe_barra", "jefe_cocina"), async (req, res) => {
  const row = await Ingredient.create(req.body);
  return res.status(201).json(row);
});

router.post("/recipes", authRequired, withRoles("administrador", "jefe_barra", "jefe_cocina"), async (req, res) => {
  const recipe = await Recipe.create({
    name: req.body.name,
    area: req.body.area,
    instructions: req.body.instructions,
    salePrice: req.body.salePrice,
    MenuItemId: req.body.menuItemId
  });

  const ingredients = req.body.ingredients || [];
  await RecipeIngredient.bulkCreate(
    ingredients.map((item) => ({ RecipeId: recipe.id, IngredientId: item.ingredientId, qty: item.qty }))
  );

  return res.status(201).json(recipe);
});

router.get("/recipes/:id/costing", authRequired, async (req, res) => {
  const recipe = await Recipe.findByPk(req.params.id, {
    include: [{ model: Ingredient, through: { attributes: ["qty"] } }, MenuItem]
  });

  if (!recipe) return res.status(404).json({ message: "Receta no encontrada" });

  const ingredients = recipe.Ingredients || [];
  const totalCost = ingredients.reduce((acc, ing) => {
    const qty = Number(ing.RecipeIngredient.qty || 0);
    const cost = Number(ing.unitCost || 0);
    return acc + qty * cost;
  }, 0);

  const salePrice = Number(recipe.salePrice || recipe.MenuItem?.price || 0);
  const profit = salePrice - totalCost;
  const marginPct = salePrice > 0 ? (profit / salePrice) * 100 : 0;

  return res.json({
    recipeId: recipe.id,
    name: recipe.name,
    area: recipe.area,
    totalCost,
    salePrice,
    profit,
    marginPct
  });
});

export default router;
