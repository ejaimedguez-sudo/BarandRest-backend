<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemCapabilitiesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $role = (string) $request->attributes->get('resolved_role', 'guest');

        return response()->json([
            'role' => $role,
            'known_roles' => config('roles.known_roles', []),
            'capabilities' => config("roles.capabilities.$role", []),
        ]);
    }
}
