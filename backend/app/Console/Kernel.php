<?php

namespace App\Console;

use App\Console\Commands\ComputeCommissions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ComputeCommissions::class,
        \App\Console\Commands\SendDailyReportsCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Example: run on the 1st of every month at 03:00 to compute previous month's commissions
        $schedule->call(function () {
            $now = new \DateTimeImmutable('now');
            $firstDayPrevMonth = $now->modify('first day of previous month')->format('Y-m-01');
            $lastDayPrevMonth = $now->modify('last day of previous month')->format('Y-m-t');
            Artisan::call('commissions:compute', [
                'from' => $firstDayPrevMonth,
                'to' => $lastDayPrevMonth,
                'percent' => 5,
            ]);
        })->monthlyOn(1, '03:00');

        // Dispatch daily report job every day at 02:00 for yesterday
        $schedule->command('reports:daily')->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Load additional command files if present
        if (is_file(__DIR__.'/Commands/ComputeCommissions.php')) {
            $this->load(__DIR__.'/Commands');
        }
        $this->load(__DIR__.'/Commands');
    }
}
