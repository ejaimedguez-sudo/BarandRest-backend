<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    public function test_reports_command_generates_files()
    {
        // Ensure migrations are applied in the testing database
        Artisan::call('migrate');

        // Run the command (uses synchronous fallback in CI)
        Artisan::call('reports:daily');

        $xlsx = glob(storage_path('app/reports/report_*.xlsx'));
        $pdf = glob(storage_path('app/reports/report_*.pdf'));
        $this->assertNotEmpty($xlsx, 'No se generó archivo XLSX de reporte');
        $this->assertNotEmpty($pdf, 'No se generó archivo PDF de reporte');
    }
}
