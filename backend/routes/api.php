<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FeaturedProductController;
use App\Http\Controllers\Api\FeaturedSectionController;
use App\Http\Controllers\Api\HomeSliderController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\WholesaleOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Ping test - En basit endpoint
Route::get('/ping', function () {
    return response()->json([
        'status' => 'API çalışıyor! ✅',
        'message' => 'Backend çalışıyor!',
        'time' => now()->toDateTimeString(),
        'memory' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB'
    ]);
});

// Database bağlantı testi
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        $categoriesCount = DB::table('categories')->count();
        $productsCount = DB::table('products')->count();
        
        return response()->json([
            'status' => 'Database bağlantısı OK ✅',
            'categories_count' => $categoriesCount,
            'products_count' => $productsCount,
            'connection' => config('database.default')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'Database hatası ❌',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Public API routes
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => now()->toIso8601String()
    ]);
});

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

// Products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/products/category/{category_slug}', [ProductController::class, 'byCategory']);

// Pages
Route::get('/pages/{slug}', [PageController::class, 'show']);

// Home Sliders
Route::get('/home-sliders', [HomeSliderController::class, 'index']);

// Featured Sections
Route::get('/featured-sections', [FeaturedSectionController::class, 'index']);

// Featured Products
Route::get('/featured-products', [FeaturedProductController::class, 'index']);

// Settings
Route::get('/settings', [SettingController::class, 'index']);

// Contact Form
Route::post('/contact', [ContactController::class, 'store']);

// Wholesale Orders
Route::post('/wholesale-orders', [WholesaleOrderController::class, 'store']);
