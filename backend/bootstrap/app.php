<?php

use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\EnsureCapability;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\RequestTracing;
use App\Http\Middleware\SecurityHeaders;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(RequestTracing::class);
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
        $schedule->command('catalog:cleanup-orphan-images --older-than-minutes=1440')->dailyAt('03:30');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (!$request->is('api/*') && !$request->expectsJson()) {
                return null;
            }

            $requestId = (string) ($request->attributes->get('request_id') ?: Str::uuid());
            $startedAt = (float) ($request->attributes->get('request_started_at') ?: microtime(true));
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            return response()->json([
                'ok' => false,
                'request_id' => $requestId,
                'message' => 'La solicitud contiene datos invalidos.',
                'errors' => $exception->errors(),
                'error' => [
                    'code' => 'validation_failed',
                    'message' => 'La solicitud contiene datos invalidos.',
                    'details' => [
                        'errors' => $exception->errors(),
                    ],
                ],
                'duration_ms' => $durationMs,
            ], 422, [
                'X-Request-Id' => $requestId,
            ]);
        });

        $exceptions->render(function (\Throwable $exception, Request $request) {
            if (!$request->is('api/*') && !$request->expectsJson()) {
                return null;
            }

            if ($exception instanceof ValidationException) {
                return null;
            }

            $requestId = (string) ($request->attributes->get('request_id') ?: Str::uuid());
            $startedAt = (float) ($request->attributes->get('request_started_at') ?: microtime(true));
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            $status = $exception instanceof HttpExceptionInterface
                ? (int) $exception->getStatusCode()
                : 500;

            $message = $status >= 500
                ? 'Error interno del servidor.'
                : (trim((string) $exception->getMessage()) !== '' ? (string) $exception->getMessage() : 'No fue posible completar la solicitud.');

            Log::error('api.request.failed', [
                'request_id' => $requestId,
                'status' => $status,
                'method' => $request->method(),
                'path' => $request->path(),
                'duration_ms' => $durationMs,
                'role' => (string) $request->header('X-USER-ROLE', 'unknown'),
                'ip' => $request->ip(),
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'request_id' => $requestId,
                'message' => $message,
                'error' => [
                    'code' => $status >= 500 ? 'internal_error' : 'request_failed',
                    'message' => $message,
                    'details' => [],
                ],
                'duration_ms' => $durationMs,
            ], $status, [
                'X-Request-Id' => $requestId,
            ]);
        });
    })->create();
