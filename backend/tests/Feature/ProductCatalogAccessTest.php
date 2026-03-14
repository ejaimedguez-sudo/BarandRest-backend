<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
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
        $type = ProductType::query()->create([
            'code' => 'PT-MP',
            'name' => 'Materia Prima',
        ]);

        $create = $this->postJson('/api/products', [
            'sku' => 'PRD-100',
            'product_type_id' => $type->id,
            'name' => 'Limon',
            'presentation' => 'Caja 20 kg',
            'image_url' => 'https://images.example.com/limon.jpg',
            'unit' => 'kg',
            'cost' => 20.5,
            'stock' => 12.75,
            'daily_consumption' => 2.55,
            'initial_stock' => 20,
            'stock_min' => 5,
            'stock_max' => 40,
            'reorder_point' => 8,
        ], [
            'X-USER-ROLE' => 'gerente',
        ]);

        $create->assertCreated()
            ->assertJsonFragment([
                'name' => 'Limon',
                'unit' => 'kg',
                'presentation' => 'Caja 20 kg',
                'image_url' => 'https://images.example.com/limon.jpg',
                'daily_consumption' => 2.55,
                'coverage_days' => 5,
            ]);

        $productId = (int) $create->json('id');

        $this->putJson("/api/products/{$productId}", [
            'name' => 'Limon Tahiti',
            'product_type_id' => $type->id,
            'presentation' => 'Bolsa 5 kg',
            'image_url' => 'https://images.example.com/limon-tahiti.jpg',
            'stock' => 18.00,
            'daily_consumption' => 3,
            'stock_min' => 6,
            'stock_max' => 42,
            'reorder_point' => 10,
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk()->assertJsonFragment([
            'name' => 'Limon Tahiti',
            'presentation' => 'Bolsa 5 kg',
            'image_url' => 'https://images.example.com/limon-tahiti.jpg',
            'reorder_point' => 10,
            'daily_consumption' => 3,
            'coverage_days' => 6,
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
            'presentation' => 'Saco 25 kg',
            'image_url' => 'https://images.example.com/azucar-morena.jpg',
            'unit' => 'kg',
            'cost' => 34.90,
            'stock' => 40,
            'daily_consumption' => 2,
            'coverage_days' => 20,
            'initial_stock' => 50,
            'stock_min' => 10,
            'stock_max' => 80,
            'reorder_point' => 15,
        ]);

        $this->getJson('/api/products', ['X-USER-ROLE' => 'admin'])
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Azucar Morena',
                'presentation' => 'Saco 25 kg',
                'image_url' => 'https://images.example.com/azucar-morena.jpg',
            ]);
    }

    public function test_product_update_and_delete_cleanup_orphan_local_image(): void
    {
        Storage::fake('public');

        $oldPath = 'catalog-images/test/product-old.jpg';
        Storage::disk('public')->put($oldPath, 'old');

        $product = Product::query()->create([
            'sku' => 'PRD-CLEAN-1',
            'name' => 'Producto con Imagen',
            'image_url' => '/storage/' . $oldPath,
            'unit' => 'pieza',
        ]);

        $this->putJson("/api/products/{$product->id}", [
            'name' => 'Producto con Imagen Editado',
            'unit' => 'pieza',
            'image_url' => 'https://images.example.com/new-product.jpg',
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk();

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertMissing($oldPath);

        $destroyPath = 'catalog-images/test/product-destroy.jpg';
        Storage::disk('public')->put($destroyPath, 'destroy');

        $productToDelete = Product::query()->create([
            'sku' => 'PRD-CLEAN-2',
            'name' => 'Producto para Borrar',
            'image_url' => '/storage/' . $destroyPath,
            'unit' => 'pieza',
        ]);

        $this->deleteJson("/api/products/{$productToDelete->id}", [], [
            'X-USER-ROLE' => 'gerente',
        ])->assertNoContent();

        $disk->assertMissing($destroyPath);
    }

    public function test_product_inventory_validation_returns_structured_error_payload(): void
    {
        $requestId = 'req-products-validation-001';

        $this->postJson('/api/products', [
            'sku' => 'PRD-VAL-001',
            'name' => 'Producto Invalido',
            'unit' => 'kg',
            'stock_min' => 10,
            'stock_max' => 5,
        ], [
            'X-USER-ROLE' => 'gerente',
            'X-Request-Id' => $requestId,
        ])->assertStatus(422)
            ->assertHeader('X-Request-Id', $requestId)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('request_id', $requestId)
            ->assertJsonPath('error.code', 'validation_failed')
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
}
