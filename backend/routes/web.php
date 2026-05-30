<?php

use App\Http\Controllers\Api\SitemapController;
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

// SEO: kök seviyede sitemap & robots (nginx exact-match → backend)
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [SitemapController::class, 'robots']);
