<?php

namespace App\Providers;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $assetVersion = (string) (@filemtime(public_path('service-worker.js')) ?: time());
        View::share('assetVersion', $assetVersion);

        // Prepend global security headers middleware
        try {
            $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
            if ($kernel instanceof HttpKernel) {
                $kernel->prependMiddleware(\App\Http\Middleware\SecurityHeaders::class);
            }
        } catch (\Throwable $e) {
            // if kernel not available during certain artisan commands, ignore
        }
    }
}
