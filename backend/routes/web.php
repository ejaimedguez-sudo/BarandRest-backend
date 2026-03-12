<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    if (view()->exists('dashboard')) {
        return view('dashboard');
    }

    return view('dashboard.index');
})->name('dashboard');
