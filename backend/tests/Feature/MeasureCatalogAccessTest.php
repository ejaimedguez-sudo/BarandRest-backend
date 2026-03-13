<?php

namespace Tests\Feature;

use App\Models\Measure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeasureCatalogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_measures_catalog_api(): void
    {
        $this->getJson('/api/measures', ['X-USER-ROLE' => 'guest'])
            ->assertStatus(403);
    }

    public function test_gerente_can_create_update_and_delete_measures(): void
    {
        $create = $this->postJson('/api/measures', [
            'name' => 'Kilogramo',
            'abbreviation' => 'kg',
            'description' => 'Medida para peso.',
        ], [
            'X-USER-ROLE' => 'gerente',
        ]);

        $create->assertCreated()
            ->assertJsonFragment([
                'name' => 'Kilogramo',
                'abbreviation' => 'kg',
            ]);

        $measureId = (int) $create->json('id');

        $this->putJson("/api/measures/{$measureId}", [
            'name' => 'Kilogramo Neto',
            'description' => 'Peso neto.',
        ], [
            'X-USER-ROLE' => 'gerente',
        ])->assertOk()->assertJsonFragment([
            'name' => 'Kilogramo Neto',
        ]);

        $this->deleteJson("/api/measures/{$measureId}", [], [
            'X-USER-ROLE' => 'gerente',
        ])->assertNoContent();

        $this->assertDatabaseMissing('measures', ['id' => $measureId]);
    }

    public function test_admin_can_list_measures_catalog(): void
    {
        Measure::query()->create([
            'name' => 'Mililitro',
            'abbreviation' => 'ml',
            'description' => 'Medida para volumen.',
        ]);

        $this->getJson('/api/measures', ['X-USER-ROLE' => 'admin'])
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Mililitro',
            ]);
    }
}
