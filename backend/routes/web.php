<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return response()->json([
        'status' => 'Backend çalışıyor! ✅',
        'time' => now()->toDateTimeString(),
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION
    ]);
});
