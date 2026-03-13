<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/catalog/products', 'catalog-products')->name('catalog.products');

Route::view('/install', 'install')->name('install');
Route::view('/viewer/api', 'api-viewer')->name('viewer.api');
