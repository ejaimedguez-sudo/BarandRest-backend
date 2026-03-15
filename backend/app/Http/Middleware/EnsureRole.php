<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        $knownRoles = config('roles.known_roles', ['guest']);

        $resolvedRole = strtolower((string) ($request->user()->role ?? $request->header('X-USER-ROLE', 'guest')));
        if (! in_array($resolvedRole, $knownRoles, true)) {
            $resolvedRole = 'guest';
        }

        $request->attributes->set('resolved_role', $resolvedRole);

        if (! empty($allowedRoles)) {
            $normalizedAllowed = array_map(static fn (string $role): string => strtolower($role), $allowedRoles);
            if (! in_array($resolvedRole, $normalizedAllowed, true)) {
                return new JsonResponse([
                    'message' => 'No autorizado para este rol.',
                    'required_roles' => $normalizedAllowed,
                    'current_role' => $resolvedRole,
                ], 403);
            }
        }

        return $next($request);
    }
}
