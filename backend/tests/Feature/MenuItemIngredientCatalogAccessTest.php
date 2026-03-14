<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\MenuItemIngredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuItemIngredientCatalogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_menu_item_ingredients_catalog_api(): void
    {
        $this->getJson('/api/menu-item-ingredients?menu_item_id=1', ['X-USER-ROLE' => 'guest'])
            ->assertStatus(403);
    }

    public function test_guest_cannot_access_menu_item_cost_histories_catalog_api(): void
    {
        $this->getJson('/api/menu-item-cost-histories?menu_item_id=1', ['X-USER-ROLE' => 'guest'])
            ->assertStatus(403);
    }

    public function test_gerente_can_create_update_and_delete_menu_item_ingredients(): void
    {
        $menuItem = MenuItem::query()->create([
            'code' => 'MI-REC-01',
            'name' => 'Mojito de la casa',
            'price' => 120,
            'cost' => 40,
            'is_recipe' => true,
        ]);

        $product = Product::query()->create([
            'sku' => 'MAT-001',
            'name' => 'Ron blanco',
            'unit' => 'ml',
            'cost' => 0.25,
            'stock' => 1000,
        ]);

        $create = $this->postJson('/api/menu-item-ingredients', [
            'menu_item_id' => $menuItem->id,
            'product_id' => $product->id,
            'quantity' => 50,
            'unit' => 'ml',
        ], [
            'X-USER-ROLE' => 'gerente',
        ]);

        $create->assertCreated()->assertJsonFragment([
            'menu_item_id' => $menuItem->id,
            'product_id' => $product->id,
            'quantity' => 50,
            'unit' => 'ml',
        ]);

        $ingredientId = (int) $create->json('id');

        $this->getJson('/api/menu-item-ingredients?menu_item_id=' . $menuItem->id, ['X-USER-ROLE' => 'gerente'])
            ->assertOk()
            ->assertJsonFragment([
                'id' => $ingredientId,
                'product_id' => $product->id,
            ]);

        $this->putJson('/api/menu-item-ingredients/' . $ingredientId, [
            'menu_item_id' => $menuItem->id,
            'product_id' => $product->id,
            'quantity' => 55.25,
            'unit' => 'ml',
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk()->assertJsonFragment([
            'id' => $ingredientId,
            'quantity' => 55.25,
            'unit' => 'ml',
        ]);

        $this->deleteJson('/api/menu-item-ingredients/' . $ingredientId, [], ['X-USER-ROLE' => 'gerente'])
            ->assertNoContent();

        $this->assertDatabaseMissing('menu_item_ingredients', ['id' => $ingredientId]);
    }

    public function test_cannot_repeat_same_product_in_same_recipe(): void
    {
        $menuItem = MenuItem::query()->create([
            'name' => 'Limonada',
            'price' => 80,
            'is_recipe' => true,
        ]);

        $product = Product::query()->create([
            'name' => 'Limon',
            'unit' => 'pza',
            'cost' => 1.50,
            'stock' => 200,
        ]);

        MenuItemIngredient::query()->create([
            'menu_item_id' => $menuItem->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit' => 'pza',
        ]);

        $this->postJson('/api/menu-item-ingredients', [
            'menu_item_id' => $menuItem->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit' => 'pza',
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_yield_calculates_consumption_and_updates_menu_item_cost_proportionally(): void
    {
        $menuItem = MenuItem::query()->create([
            'name' => 'Negroni',
            'price' => 150,
            'cost' => 0,
            'is_recipe' => true,
        ]);

        $product = Product::query()->create([
            'name' => 'Gin premium 1L',
            'unit' => 'botella',
            'cost' => 400,
            'stock' => 10,
        ]);

        $create = $this->postJson('/api/menu-item-ingredients', [
            'menu_item_id' => $menuItem->id,
            'product_id' => $product->id,
            'cocktail_yield' => 20,
            'unit' => 'ml',
        ], [
            'X-USER-ROLE' => 'gerente',
        ]);

        $create->assertCreated()->assertJsonFragment([
            'menu_item_id' => $menuItem->id,
            'product_id' => $product->id,
            'cocktail_yield' => 20,
            'consumption_ml' => 50,
            'quantity' => 50,
        ]);

        $menuItem->refresh();
        $this->assertEquals(20.0, (float) $menuItem->cost);
    }

    public function test_cost_history_is_logged_when_recipe_cost_changes(): void
    {
        $menuItem = MenuItem::query()->create([
            'name' => 'Spritz',
            'price' => 180,
            'cost' => 0,
            'is_recipe' => true,
        ]);

        $product = Product::query()->create([
            'name' => 'Aperitivo 1L',
            'unit' => 'botella',
            'cost' => 300,
            'stock' => 8,
        ]);

        $create = $this->postJson('/api/menu-item-ingredients', [
            'menu_item_id' => $menuItem->id,
            'product_id' => $product->id,
            'consumption_ml' => 100,
            'unit' => 'ml',
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertCreated();

        $ingredientId = (int) $create->json('id');

        $this->assertDatabaseHas('menu_item_cost_histories', [
            'menu_item_id' => $menuItem->id,
            'action' => 'ingredient_created',
            'actor_role' => 'gerente',
            'new_cost' => 30.00,
        ]);

        $this->putJson('/api/menu-item-ingredients/' . $ingredientId, [
            'consumption_ml' => 200,
            'unit' => 'ml',
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk();

        $this->assertDatabaseHas('menu_item_cost_histories', [
            'menu_item_id' => $menuItem->id,
            'action' => 'ingredient_updated',
            'actor_role' => 'gerente',
            'previous_cost' => 30.00,
            'new_cost' => 60.00,
            'difference' => 30.00,
        ]);

        $this->getJson('/api/menu-item-cost-histories?menu_item_id=' . $menuItem->id, [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk()
            ->assertJsonFragment([
                'menu_item_id' => $menuItem->id,
                'action' => 'ingredient_updated',
                'actor_role' => 'gerente',
            ]);
    }
}
