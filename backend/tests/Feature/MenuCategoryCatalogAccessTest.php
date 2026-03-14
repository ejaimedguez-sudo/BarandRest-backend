<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuCategoryCatalogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_menu_categories_catalog_api(): void
    {
        $this->getJson('/api/menu-categories', ['X-USER-ROLE' => 'guest'])
            ->assertStatus(403);
    }

    public function test_gerente_can_create_update_and_delete_menu_categories(): void
    {
        $create = $this->postJson('/api/menu-categories', [
            'code' => 'CAT-COCTEL',
            'name' => 'Coctel',
            'description' => 'Bebidas preparadas.',
            'sort_order' => 10,
        ], [
            'X-USER-ROLE' => 'gerente',
        ]);

        $create->assertCreated()->assertJsonFragment([
            'name' => 'Coctel',
            'code' => 'CAT-COCTEL',
        ]);

        $categoryId = (int) $create->json('id');

        $this->putJson("/api/menu-categories/{$categoryId}", [
            'name' => 'Coctel de Autor',
            'sort_order' => 12,
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk()->assertJsonFragment([
            'name' => 'Coctel de Autor',
        ]);

        $this->deleteJson("/api/menu-categories/{$categoryId}", [], [
            'X-USER-ROLE' => 'gerente',
        ])->assertNoContent();

        $this->assertDatabaseMissing('menu_categories', ['id' => $categoryId]);
    }

    public function test_admin_can_list_menu_categories_catalog(): void
    {
        MenuCategory::query()->create([
            'code' => 'CAT-POSTRE',
            'name' => 'Postre',
            'description' => 'Opciones dulces.',
            'sort_order' => 30,
        ]);

        $this->getJson('/api/menu-categories', ['X-USER-ROLE' => 'admin'])
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Postre',
                'code' => 'CAT-POSTRE',
            ]);
    }

    public function test_menu_category_duplicate_name_returns_structured_error_payload(): void
    {
        MenuCategory::query()->create([
            'code' => 'CAT-UNICA',
            'name' => 'Categoria Unica',
        ]);

        $requestId = 'req-menu-category-dup-001';

        $this->postJson('/api/menu-categories', [
            'code' => 'CAT-UNICA-2',
            'name' => 'Categoria Unica',
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
