<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TableRestaurantController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\ReportsQueueController;
use App\Http\Controllers\API\ReportExportController;
use App\Http\Controllers\API\PrintController;
use App\Http\Controllers\API\AuthUserController;
use App\Http\Controllers\API\SystemCapabilitiesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', AuthUserController::class);

// API resource routes
Route::apiResource('products', ProductController::class);
Route::apiResource('menu-items', MenuItemController::class);
Route::apiResource('orders', OrderController::class);
Route::apiResource('tables', TableRestaurantController::class);
Route::apiResource('customers', CustomerController::class);
Route::apiResource('commissions', CommissionController::class);
Route::apiResource('expenses', ExpenseController::class);

// Reports and integrations
use App\Http\Controllers\API\ReportsController;

Route::get('reports/daily', [ReportsController::class, 'daily']);
Route::get('reports/sales', [ReportsController::class, 'sales']);
Route::get('reports/weekly', [ReportsController::class, 'weekly']);
Route::get('reports/monthly', [ReportsController::class, 'monthly']);
Route::get('reports/yearly', [ReportsController::class, 'yearly']);
Route::get('reports/download/{filename}', [\App\Http\Controllers\API\ReportDownloadController::class, 'download']);
Route::post('reports/daily/queue', [\App\Http\Controllers\API\ReportsQueueController::class, 'queueDaily'])->middleware('role:admin,gerente');
Route::get('reports/export/excel', [ReportExportController::class, 'exportExcel']);
Route::get('reports/export/pdf', [ReportExportController::class, 'exportPdf']);
Route::post('commissions/compute', [\App\Http\Controllers\API\CommissionController::class, 'compute'])->middleware('role:admin,gerente');
Route::post('print/ticket', [PrintController::class, 'ticket'])->middleware('role:admin,gerente,caja,cocina,mesero');

// Dashboard
use App\Http\Controllers\API\DashboardController;
Route::get('dashboard/metrics', [DashboardController::class, 'metrics'])->middleware(\App\Http\Middleware\DashboardApiKeyAuth::class);
Route::post('dashboard/clear-cache', [DashboardController::class, 'clearCache'])
    ->middleware([\App\Http\Middleware\DashboardApiKeyAuth::class, 'role:admin,gerente']);

Route::get('system/capabilities', SystemCapabilitiesController::class)->middleware('role');
