<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Component gallery (smoke-test) — non-production only
if (! app()->environment('production')) {
    Route::get('/__components', function () {
        return view('components-gallery');
    })->name('dev.components');
}

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.placeholder');
    })->name('home');
});
