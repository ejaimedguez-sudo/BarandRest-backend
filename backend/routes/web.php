<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (view()->exists('dashboard')) {
        return view('dashboard');
    }

    return view('dashboard.index');
})->name('dashboard');
