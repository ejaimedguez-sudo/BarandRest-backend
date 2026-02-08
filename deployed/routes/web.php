<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Include API routes under /api prefix for environments where RouteServiceProvider
// does not auto-load routes/api.php (simplified bootstrap).
use Illuminate\Support\Facades\Route as RouteFacade;
RouteFacade::prefix('api')->middleware('api')->group(function () {
    require __DIR__.'/api.php';
});
