import { Recipe, Ingredient, InventoryMovement } from "../models.js";

export async function consumeInventoryForOrderItem({ menuItemId, orderQty, userId, transaction }) {
  const recipe = await Recipe.findOne({
    where: { MenuItemId: menuItemId },
    include: [{ model: Ingredient, through: { attributes: ["qty"] } }],
    transaction
  });

  if (!recipe) {
    return { consumed: false, details: [] };
  }

  const details = [];
  for (const ingredient of recipe.Ingredients || []) {
    const baseQty = Number(ingredient.RecipeIngredient?.qty || 0);
    const totalQty = baseQty * Number(orderQty || 0);

    ingredient.stockQty = Number(ingredient.stockQty) - totalQty;
    await ingredient.save({ transaction });

    await InventoryMovement.create(
      {
        IngredientId: ingredient.id,
        type: "out",
        qty: totalQty,
        reason: `Consumo por orden de menu_item ${menuItemId}`,
        performedById: userId || null
      },
      { transaction }
    );

    details.push({
      ingredientId: ingredient.id,
      ingredientName: ingredient.name,
      consumedQty: totalQty,
      unit: ingredient.unit,
      remainingStock: Number(ingredient.stockQty)
    });
  }

  return { consumed: true, details };
}
