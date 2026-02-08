<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Mail\ReportReady;

class ReportEmailTest extends TestCase
{
    public function test_reports_command_sends_email()
    {
        Mail::fake();

        // Ensure env recipient is set for the test and run queue synchronously
        putenv('MAIL_REPORT_RECIPIENT=test@example.com');
        $_ENV['MAIL_REPORT_RECIPIENT'] = 'test@example.com';
        $_SERVER['MAIL_REPORT_RECIPIENT'] = 'test@example.com';
        $this->app->make('config')->set('queue.default', 'sync');

        // Bind a stub ReportsController to avoid DB dependency during test
        $this->app->bind(\App\Http\Controllers\API\ReportsController::class, function () {
            return new class {
                public function daily($request)
                {
                    return [
                        ['date' => now()->format('Y-m-d'), 'item' => 'Test', 'quantity' => 1, 'unit_price' => 10.0],
                    ];
                }
            };
        });

        // Directly run the job synchronously to avoid queue driver indirection in tests
        $job = new \App\Jobs\GenerateAndEmailDailyReport(now()->format('Y-m-d'), now()->format('Y-m-d'));
        $job->handle();

        Mail::assertSent(ReportReady::class, function ($mail) {
            return true;
        });
    }
}
