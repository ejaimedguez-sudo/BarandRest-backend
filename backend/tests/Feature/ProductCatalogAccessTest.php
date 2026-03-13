<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_products_catalog_api(): void
    {
        $this->getJson('/api/products', ['X-USER-ROLE' => 'guest'])
            ->assertStatus(403);
    }

    public function test_gerente_can_create_update_and_delete_products(): void
    {
        $create = $this->postJson('/api/products', [
            'sku' => 'PRD-100',
            'name' => 'Limon',
            'unit' => 'kg',
            'cost' => 20.5,
            'stock' => 12.75,
            'reorder_level' => 5,
        ], [
            'X-USER-ROLE' => 'gerente',
        ]);

        $create->assertCreated()
            ->assertJsonFragment([
                'name' => 'Limon',
                'unit' => 'kg',
            ]);

        $productId = (int) $create->json('id');

        $this->putJson("/api/products/{$productId}", [
            'name' => 'Limon Tahiti',
            'stock' => 18.00,
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk()->assertJsonFragment([
            'name' => 'Limon Tahiti',
        ]);

        $this->deleteJson("/api/products/{$productId}", [], [
            'X-USER-ROLE' => 'gerente',
        ])->assertNoContent();

        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }

    public function test_admin_can_list_products_catalog(): void
    {
        Product::query()->create([
            'sku' => 'PRD-200',
            'name' => 'Azucar Morena',
            'unit' => 'kg',
            'cost' => 34.90,
            'stock' => 40,
            'reorder_level' => 8,
        ]);

        $this->getJson('/api/products', ['X-USER-ROLE' => 'admin'])
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Azucar Morena',
            ]);
    }
}
