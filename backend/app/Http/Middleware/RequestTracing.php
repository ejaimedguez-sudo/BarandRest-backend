<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestTracing
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestIdHeader = trim((string) $request->headers->get('X-Request-Id', ''));
        $requestId = $requestIdHeader !== '' ? $requestIdHeader : (string) Str::uuid();

        $request->attributes->set('request_id', $requestId);
        $request->attributes->set('request_started_at', microtime(true));

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }
}
