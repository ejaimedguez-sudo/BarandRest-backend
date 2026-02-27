<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportsApiCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_and_yearly_reports_work_on_default_local_db(): void
    {
        DB::table('orders')->insert([
            'table_id' => null,
            'user_id' => null,
            'total' => 120.50,
            'cost' => 70.00,
            'status' => 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->getJson('/api/reports/monthly')->assertOk();
        $this->getJson('/api/reports/yearly')->assertOk();
    }

    public function test_api_post_endpoints_are_not_blocked_by_csrf(): void
    {
        $dailyQueue = $this->postJson('/api/reports/daily/queue', []);
        $this->assertNotSame(419, $dailyQueue->getStatusCode());

        $printTicket = $this->postJson('/api/print/ticket', []);
        $this->assertNotSame(419, $printTicket->getStatusCode());
    }
}
