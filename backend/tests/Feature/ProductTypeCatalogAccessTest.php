<?php

namespace Tests\Feature;

use App\Models\ProductType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTypeCatalogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_product_types_catalog_api(): void
    {
        $this->getJson('/api/product-types', ['X-USER-ROLE' => 'guest'])
            ->assertStatus(403);
    }

    public function test_gerente_can_create_update_and_delete_product_types(): void
    {
        $create = $this->postJson('/api/product-types', [
            'code' => 'PT-BAR',
            'name' => 'Bar',
            'description' => 'Productos del bar.',
        ], [
            'X-USER-ROLE' => 'gerente',
        ]);

        $create->assertCreated()->assertJsonFragment([
            'name' => 'Bar',
            'code' => 'PT-BAR',
        ]);

        $typeId = (int) $create->json('id');

        $this->putJson("/api/product-types/{$typeId}", [
            'name' => 'Bar Premium',
            'description' => 'Bebidas y licores premium.',
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk()->assertJsonFragment([
            'name' => 'Bar Premium',
        ]);

        $this->deleteJson("/api/product-types/{$typeId}", [], [
            'X-USER-ROLE' => 'gerente',
        ])->assertNoContent();

        $this->assertDatabaseMissing('product_types', ['id' => $typeId]);
    }

    public function test_admin_can_list_product_types_catalog(): void
    {
        ProductType::query()->create([
            'code' => 'PT-COC',
            'name' => 'Cocina',
            'description' => 'Productos de cocina.',
        ]);

        $this->getJson('/api/product-types', ['X-USER-ROLE' => 'admin'])
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Cocina',
                'code' => 'PT-COC',
            ]);
    }

    public function test_product_type_duplicate_name_returns_structured_error_payload(): void
    {
        ProductType::query()->create([
            'code' => 'PT-BASE',
            'name' => 'Base',
        ]);

        $requestId = 'req-product-type-dup-001';

        $this->postJson('/api/product-types', [
            'code' => 'PT-BASE-2',
            'name' => 'Base',
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
