<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\GenerateAndEmailDailyReport;

class SendDailyReportsCommand extends Command
{
    protected $signature = 'reports:daily {date?} {email?} {group_by?}';
    protected $description = 'Generate and email daily reports for a date (defaults to yesterday)';

    public function handle()
    {
        $date = $this->argument('date') ?? now()->subDay()->format('Y-m-d');
        $email = $this->argument('email') ?? env('MAIL_REPORT_RECIPIENT');
        $group = $this->argument('group_by') ?? null;

        try {
            // Attempt to dispatch to queue
            GenerateAndEmailDailyReport::dispatch($date, $date);
            $this->info("Dispatched report job for $date");
        } catch (\Illuminate\Database\QueryException $e) {
            // If queue table missing or DB issue, run synchronously
            $this->warn('Queue DB not available, running job synchronously.');
            $job = new \App\Jobs\GenerateAndEmailDailyReport($date, $date);
            $job->handle();
            $this->info("Report generated synchronously for $date");
        }
        return 0;
    }
}
