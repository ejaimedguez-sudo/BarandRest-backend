<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\ProductType;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuItemCatalogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_menu_items_catalog_api(): void
    {
        $this->getJson('/api/menu-items', ['X-USER-ROLE' => 'guest'])
            ->assertStatus(403);
    }

    public function test_gerente_can_create_update_and_delete_menu_items(): void
    {
        $type = ProductType::query()->create([
            'code' => 'PT-BAR',
            'name' => 'Bar',
        ]);

        $menuCategory = MenuCategory::query()->create([
            'code' => 'CAT-COCTEL',
            'name' => 'Coctel',
        ]);

        $create = $this->postJson('/api/menu-items', [
            'code' => 'MI-100',
            'name' => 'Mojito Clasico',
            'product_type_id' => $type->id,
            'menu_category_id' => $menuCategory->id,
            'description' => 'Ron, menta y limon.',
            'image_url' => 'https://images.example.com/mojito.jpg',
            'price' => 145.00,
            'manual_cost' => 48.50,
            'cost' => 48.50,
            'profit_margin_percent' => 33.10,
            'is_recipe' => true,
            'prep_time_minutes' => 8,
            'dish' => 'Bebida',
            'kitchen' => 'Barra',
            'servings' => 1,
            'calories' => 210,
            'equipment' => 'Shaker y muddler',
        ], [
            'X-USER-ROLE' => 'gerente',
        ]);

        $create->assertCreated()->assertJsonFragment([
            'name' => 'Mojito Clasico',
            'code' => 'MI-100',
            'image_url' => 'https://images.example.com/mojito.jpg',
            'price' => 145,
            'manual_cost' => 48.5,
            'profit_margin_percent' => 33.1,
            'prep_time_minutes' => 8,
            'dish' => 'Bebida',
            'kitchen' => 'Barra',
            'servings' => 1,
            'calories' => 210,
            'equipment' => 'Shaker y muddler',
        ]);

        $itemId = (int) $create->json('id');

        $this->putJson("/api/menu-items/{$itemId}", [
            'code' => 'MI-100X',
            'name' => 'Mojito Especial',
            'product_type_id' => $type->id,
            'menu_category_id' => $menuCategory->id,
            'price' => 150.00,
            'manual_cost' => 50.00,
            'cost' => 50.00,
            'profit_margin_percent' => 35.00,
            'is_recipe' => false,
            'description' => 'Version premium.',
            'image_url' => 'https://images.example.com/mojito-especial.jpg',
            'prep_time_minutes' => 10,
            'dish' => 'Coctel de autor',
            'kitchen' => 'Barra fria',
            'servings' => 2,
            'calories' => 250,
            'equipment' => 'Cuchara trenzada',
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk()->assertJsonFragment([
            'name' => 'Mojito Especial',
            'code' => 'MI-100X',
            'image_url' => 'https://images.example.com/mojito-especial.jpg',
            'price' => 150,
            'manual_cost' => 50,
            'profit_margin_percent' => 35,
            'prep_time_minutes' => 10,
            'dish' => 'Coctel de autor',
            'kitchen' => 'Barra fria',
            'servings' => 2,
            'calories' => 250,
            'equipment' => 'Cuchara trenzada',
        ]);

        $this->deleteJson("/api/menu-items/{$itemId}", [], [
            'X-USER-ROLE' => 'gerente',
        ])->assertNoContent();

        $this->assertDatabaseMissing('menu_items', ['id' => $itemId]);
    }

    public function test_admin_can_list_menu_items_catalog(): void
    {
        $type = ProductType::query()->create([
            'code' => 'PT-BAR',
            'name' => 'Bar',
        ]);

        $menuCategory = MenuCategory::query()->create([
            'code' => 'CAT-DEST',
            'name' => 'Destilado',
        ]);

        MenuItem::query()->create([
            'code' => 'MI-200',
            'name' => 'Negroni',
            'product_type_id' => $type->id,
            'menu_category_id' => $menuCategory->id,
            'description' => 'Gin, campari y vermut rojo.',
            'image_url' => 'https://images.example.com/negroni.jpg',
            'price' => 165,
            'manual_cost' => 59,
            'cost' => 59,
            'profit_margin_percent' => 64.24,
            'is_recipe' => false,
            'prep_time_minutes' => 4,
            'dish' => 'Aperitivo',
            'kitchen' => 'Barra',
            'servings' => 1,
            'calories' => 180,
            'equipment' => 'Vaso mezclador',
        ]);

        $this->getJson('/api/menu-items', ['X-USER-ROLE' => 'admin'])
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Negroni',
                'code' => 'MI-200',
                'image_url' => 'https://images.example.com/negroni.jpg',
                'manual_cost' => 59,
                'profit_margin_percent' => 64.24,
                'prep_time_minutes' => 4,
                'dish' => 'Aperitivo',
                'kitchen' => 'Barra',
                'servings' => 1,
                'calories' => 180,
                'equipment' => 'Vaso mezclador',
            ]);
    }

    public function test_admin_must_respect_minimum_margin_rule_for_category(): void
    {
        $type = ProductType::query()->create([
            'code' => 'PT-BAR',
            'name' => 'Bar',
        ]);

        $menuCategory = MenuCategory::query()->create([
            'code' => 'CAT-COCTEL',
            'name' => 'Coctel',
        ]);

        $requestId = 'req-menu-item-margin-001';

        $this->postJson('/api/menu-items', [
            'code' => 'MI-LOW-MARGIN',
            'name' => 'Coctel bajo margen',
            'product_type_id' => $type->id,
            'menu_category_id' => $menuCategory->id,
            'price' => 100,
            'cost' => 90,
            'profit_margin_percent' => 10,
            'is_recipe' => false,
        ], [
            'X-USER-ROLE' => 'admin',
            'X-Request-Id' => $requestId,
        ])->assertStatus(422)
            ->assertHeader('X-Request-Id', $requestId)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('request_id', $requestId)
            ->assertJsonPath('error.code', 'validation_failed')
            ->assertJsonValidationErrors(['profit_margin_percent'])
            ->assertJsonStructure([
                'ok',
                'request_id',
                'message',
                'errors',
                'error' => [
                    'code',
                    'message',
                    'details' => ['errors'],
                ],
                'duration_ms',
            ]);
    }

    public function test_gerente_can_override_minimum_margin_rule_for_category(): void
    {
        $type = ProductType::query()->create([
            'code' => 'PT-BAR',
            'name' => 'Bar',
        ]);

        $menuCategory = MenuCategory::query()->create([
            'code' => 'CAT-COCTEL',
            'name' => 'Coctel',
        ]);

        $this->postJson('/api/menu-items', [
            'code' => 'MI-MARGIN-OVR',
            'name' => 'Coctel promocional',
            'product_type_id' => $type->id,
            'menu_category_id' => $menuCategory->id,
            'price' => 100,
            'cost' => 90,
            'profit_margin_percent' => 10,
            'is_recipe' => false,
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertCreated();
    }

    public function test_menu_item_update_and_delete_cleanup_orphan_local_image(): void
    {
        Storage::fake('public');

        $oldPath = 'catalog-images/test/menu-item-old.jpg';
        Storage::disk('public')->put($oldPath, 'old');

        $type = ProductType::query()->create([
            'code' => 'PT-CLEAN',
            'name' => 'Tipo Clean',
        ]);

        $menuCategory = MenuCategory::query()->create([
            'code' => 'CAT-CLEAN',
            'name' => 'Categoria Clean',
        ]);

        $menuItem = MenuItem::query()->create([
            'code' => 'MI-CLEAN-1',
            'name' => 'Item con Imagen',
            'product_type_id' => $type->id,
            'menu_category_id' => $menuCategory->id,
            'image_url' => '/storage/'.$oldPath,
            'price' => 100,
            'cost' => 50,
            'profit_margin_percent' => 50,
            'is_recipe' => false,
        ]);

        $this->putJson("/api/menu-items/{$menuItem->id}", [
            'name' => 'Item con Imagen Editado',
            'image_url' => 'https://images.example.com/new-item.jpg',
            'price' => 120,
            'cost' => 60,
            'is_recipe' => false,
            'product_type_id' => $type->id,
            'menu_category_id' => $menuCategory->id,
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk();

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertMissing($oldPath);

        $destroyPath = 'catalog-images/test/menu-item-destroy.jpg';
        Storage::disk('public')->put($destroyPath, 'destroy');

        $menuItemToDelete = MenuItem::query()->create([
            'code' => 'MI-CLEAN-2',
            'name' => 'Item para Borrar',
            'product_type_id' => $type->id,
            'menu_category_id' => $menuCategory->id,
            'image_url' => '/storage/'.$destroyPath,
            'price' => 100,
            'cost' => 50,
            'profit_margin_percent' => 50,
            'is_recipe' => false,
        ]);

        $this->deleteJson("/api/menu-items/{$menuItemToDelete->id}", [], [
            'X-USER-ROLE' => 'gerente',
        ])->assertNoContent();

        $disk->assertMissing($destroyPath);
    }
}
