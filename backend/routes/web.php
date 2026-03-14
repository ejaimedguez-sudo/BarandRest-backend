<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/catalog/measures', 'catalog-measures')->name('catalog.measures');
Route::view('/catalog/product-types', 'catalog-product-types')->name('catalog.product-types');
Route::view('/catalog/menu-categories', 'catalog-menu-categories')->name('catalog.menu-categories');
Route::view('/catalog/products', 'catalog-products')->name('catalog.products');
Route::view('/catalog/menu-items', 'catalog-menu-items')->name('catalog.menu-items');

Route::view('/install', 'install')->name('install');
Route::view('/viewer/api', 'api-viewer')->name('viewer.api');
