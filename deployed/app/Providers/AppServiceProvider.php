<?php

namespace App\Providers;

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
        // Prepend global security headers middleware
        try {
            $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
            $kernel->prependMiddleware(\App\Http\Middleware\SecurityHeaders::class);
        } catch (\Throwable $e) {
            // if kernel not available during certain artisan commands, ignore
        }
    }
}
