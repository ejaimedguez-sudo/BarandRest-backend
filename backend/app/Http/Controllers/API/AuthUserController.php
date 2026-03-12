<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthUserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
