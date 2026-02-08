<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request and add security headers.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Basic security headers
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=()');

        // Content Security Policy - conservative default
        $csp = "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;";
        $response->headers->set('Content-Security-Policy', $csp);

        // HSTS for HTTPS (only if request is secure)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        }

        return $response;
    }
}
