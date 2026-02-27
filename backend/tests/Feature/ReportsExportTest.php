<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReportsExportTest extends TestCase
{
    public function test_placeholder_queue_export()
    {
        $this->markTestSkipped('Integration test placeholder - requires Laravel environment and queue/mail config');
    }
}
