<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DashboardApiKeyAuth
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('X-API-KEY') ?: $request->query('api_key');
        $key = (string) config('dashboard.api_key', 'change_me_to_a_secure_value');

        if (!$key || !$header || !hash_equals($key, $header)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
