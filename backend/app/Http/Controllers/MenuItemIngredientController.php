<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\MenuItemCostHistory;
use App\Models\MenuItemIngredient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MenuItemIngredientController extends Controller
{
    private function applyYieldRules(array $data, ?MenuItemIngredient $existing = null): array
    {
        $hasYield = array_key_exists('cocktail_yield', $data);
        $hasConsumption = array_key_exists('consumption_ml', $data);
        $hasQuantity = array_key_exists('quantity', $data);

        $yield = $hasYield && $data['cocktail_yield'] !== null ? (float) $data['cocktail_yield'] : null;
        $consumption = $hasConsumption && $data['consumption_ml'] !== null ? (float) $data['consumption_ml'] : null;

        if ($yield !== null && $yield > 0) {
            $computedConsumption = round(1000 / $yield, 3);
            $data['cocktail_yield'] = round($yield, 3);
            $data['consumption_ml'] = $computedConsumption;
            $data['quantity'] = $computedConsumption;
            if (! array_key_exists('unit', $data) || empty($data['unit'])) {
                $data['unit'] = 'ml';
            }

            return $data;
        }

        if ($consumption !== null && $consumption > 0) {
            $data['consumption_ml'] = round($consumption, 3);
            $data['quantity'] = round($consumption, 3);
            if (! array_key_exists('unit', $data) || empty($data['unit'])) {
                $data['unit'] = 'ml';
            }

            return $data;
        }

        if ($hasYield || $hasConsumption) {
            $data['cocktail_yield'] = null;
            $data['consumption_ml'] = null;
        }

        if (! $hasQuantity && ! $hasYield && ! $hasConsumption && ! $existing) {
            throw ValidationException::withMessages([
                'quantity' => ['Debes indicar consumo manual o rendimiento por botella.'],
            ]);
        }

        if ($hasQuantity && (! is_numeric($data['quantity']) || (float) $data['quantity'] <= 0)) {
            throw ValidationException::withMessages([
                'quantity' => ['La cantidad debe ser mayor a 0.'],
            ]);
        }

        return $data;
    }

    private function syncMenuItemCost(int $menuItemId, string $action, ?string $actorRole = null, array $context = []): void
    {
        $menuItem = MenuItem::query()->with('ingredients.product')->find($menuItemId);
        if (! $menuItem) {
            return;
        }

        $previousCost = is_numeric($menuItem->cost) ? round((float) $menuItem->cost, 2) : null;
        $cost = round($menuItem->calculateCostFromIngredients(), 2);
        $update = ['cost' => $cost];

        $price = is_numeric($menuItem->price) ? (float) $menuItem->price : 0.0;
        if ($price > 0) {
            $update['profit_margin_percent'] = round((($price - $cost) / $price) * 100, 2);
        }

        $menuItem->update($update);

        if ($previousCost === null || abs($cost - $previousCost) >= 0.01) {
            MenuItemCostHistory::query()->create([
                'menu_item_id' => $menuItem->id,
                'action' => $action,
                'actor_role' => $actorRole,
                'previous_cost' => $previousCost,
                'new_cost' => $cost,
                'difference' => $previousCost === null ? null : round($cost - $previousCost, 2),
                'context' => $context,
            ]);
        }
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'menu_item_id' => 'required|integer|exists:menu_items,id',
        ]);

        $ingredients = MenuItemIngredient::query()
            ->where('menu_item_id', $data['menu_item_id'])
            ->with('product:id,name,unit,cost,stock')
            ->orderBy('id')
            ->get();

        return response()->json($ingredients);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'menu_item_id' => 'required|integer|exists:menu_items,id',
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                Rule::unique('menu_item_ingredients', 'product_id')->where(
                    fn ($query) => $query->where('menu_item_id', $request->input('menu_item_id'))
                ),
            ],
            'quantity' => 'nullable|numeric|gt:0',
            'cocktail_yield' => 'nullable|numeric|gt:0',
            'consumption_ml' => 'nullable|numeric|gt:0',
            'unit' => 'nullable|string|max:50',
        ], [
            'product_id.unique' => 'El material ya esta agregado a esta receta.',
            'quantity.gt' => 'La cantidad debe ser mayor a 0.',
            'cocktail_yield.gt' => 'El rendimiento debe ser mayor a 0.',
            'consumption_ml.gt' => 'El consumo en ml debe ser mayor a 0.',
        ]);

        $data = $this->applyYieldRules($data);

        $ingredient = MenuItemIngredient::query()->create($data);
        $this->syncMenuItemCost(
            (int) $ingredient->menu_item_id,
            'ingredient_created',
            $request->header('X-USER-ROLE'),
            ['ingredient_id' => $ingredient->id, 'product_id' => $ingredient->product_id]
        );
        $ingredient->load('product:id,name,unit,cost,stock');

        return response()->json($ingredient, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function show(MenuItemIngredient $menuItemIngredient)
    {
        $menuItemIngredient->load('product:id,name,unit,cost,stock');

        return response()->json($menuItemIngredient);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function update(Request $request, MenuItemIngredient $menuItemIngredient)
    {
        $data = $request->validate([
            'menu_item_id' => 'sometimes|required|integer|exists:menu_items,id',
            'product_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:products,id',
                Rule::unique('menu_item_ingredients', 'product_id')
                    ->where(fn ($query) => $query->where('menu_item_id', $request->input('menu_item_id', $menuItemIngredient->menu_item_id)))
                    ->ignore($menuItemIngredient->id),
            ],
            'quantity' => 'nullable|numeric|gt:0',
            'cocktail_yield' => 'nullable|numeric|gt:0',
            'consumption_ml' => 'nullable|numeric|gt:0',
            'unit' => 'nullable|string|max:50',
        ], [
            'product_id.unique' => 'El material ya esta agregado a esta receta.',
            'quantity.gt' => 'La cantidad debe ser mayor a 0.',
            'cocktail_yield.gt' => 'El rendimiento debe ser mayor a 0.',
            'consumption_ml.gt' => 'El consumo en ml debe ser mayor a 0.',
        ]);

        $data = $this->applyYieldRules($data, $menuItemIngredient);

        $menuItemIngredient->update($data);
        $this->syncMenuItemCost(
            (int) $menuItemIngredient->menu_item_id,
            'ingredient_updated',
            $request->header('X-USER-ROLE'),
            ['ingredient_id' => $menuItemIngredient->id, 'product_id' => $menuItemIngredient->product_id]
        );
        $menuItemIngredient->load('product:id,name,unit,cost,stock');

        return response()->json($menuItemIngredient);
    }

    public function destroy(MenuItemIngredient $menuItemIngredient)
    {
        $menuItemId = (int) $menuItemIngredient->menu_item_id;
        $ingredientId = (int) $menuItemIngredient->id;
        $productId = (int) $menuItemIngredient->product_id;
        $menuItemIngredient->delete();
        $this->syncMenuItemCost(
            $menuItemId,
            'ingredient_deleted',
            request()->header('X-USER-ROLE'),
            ['ingredient_id' => $ingredientId, 'product_id' => $productId]
        );

        return response()->json(null, 204);
    }
}
