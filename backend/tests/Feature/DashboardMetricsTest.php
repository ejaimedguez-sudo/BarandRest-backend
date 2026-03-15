<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrics_endpoint_requires_api_key_and_returns_structure()
    {
        // Use configured API key from config (compatible with config cache)
        $apiKey = (string) config('dashboard.api_key', 'change_me_to_a_secure_value');

        // Create a closed order to appear in metrics
        DB::table('orders')->insert([
            'table_id' => null,
            'user_id' => null,
            'total' => 100.50,
            'cost' => 60.00,
            'status' => 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Call without key -> unauthorized
        $this->getJson('/api/dashboard/metrics')->assertStatus(401);

        // Call with key
        $res = $this->withHeaders(['X-API-KEY' => $apiKey])->getJson('/api/dashboard/metrics');
        $res->assertStatus(200)
            ->assertJsonStructure(['dailySales', 'weeklySales', 'topProducts', 'lowStock']);
    }
}
