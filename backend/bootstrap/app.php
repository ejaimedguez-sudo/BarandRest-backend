<?php

use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Artisan;
use App\Http\Middleware\EnsureCapability;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\SecurityHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);

        $middleware->alias([
            'capability' => EnsureCapability::class,
            'role' => EnsureRole::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
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

        $schedule->command('reports:daily')->dailyAt('02:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
