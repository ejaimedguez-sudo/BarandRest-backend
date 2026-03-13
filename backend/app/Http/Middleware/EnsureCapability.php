<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCapability
{
    public function handle(Request $request, Closure $next, string ...$requiredCapabilities): Response
    {
        if (empty($requiredCapabilities)) {
            return $next($request);
        }

        $resolvedRole = strtolower((string) $request->attributes->get('resolved_role', 'guest'));
        $roleCapabilities = config("roles.capabilities.$resolvedRole", []);

        $normalizedRequired = array_values(array_filter(array_map(
            static fn (string $capability): string => strtolower(trim($capability)),
            $requiredCapabilities
        )));

        $hasAllCapabilities = !array_diff($normalizedRequired, array_map('strtolower', $roleCapabilities));

        if (!$hasAllCapabilities) {
            return new JsonResponse([
                'message' => 'No autorizado para esta capacidad.',
                'required_capabilities' => $normalizedRequired,
                'current_role' => $resolvedRole,
                'role_capabilities' => $roleCapabilities,
            ], 403);
        }

        return $next($request);
    }
}
