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

// Ping test
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

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Bilinmeyen rotalara 404 JSON döndür
Route::fallback(function () {
    return response()->json(['message' => 'Sayfa bulunamadı.'], 404);
});

// Health
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String()
    ]);
});

// Kategoriler
Route::get('/kategoriler', [CategoryController::class, 'index']);
Route::get('/kategoriler/{slug}', [CategoryController::class, 'show']);

// Ürünler
Route::get('/urunler', [ProductController::class, 'index']);
Route::get('/urunler/{slug}', [ProductController::class, 'show']);
Route::get('/urunler/kategori/{category_slug}', [ProductController::class, 'byCategory']);

// Sayfalar
Route::get('/sayfalar/{slug}', [PageController::class, 'show']);

// Anasayfa Slider
Route::get('/anasayfa-slider', [HomeSliderController::class, 'index']);

// Öne Çıkan Bölümler
Route::get('/one-cikan-bolumler', [FeaturedSectionController::class, 'index']);

// Öne Çıkan Ürünler
Route::get('/one-cikan-urunler', [FeaturedProductController::class, 'index']);

// Ayarlar
Route::get('/ayarlar', [SettingController::class, 'index']);

// İletişim Formu
Route::post('/iletisim', [ContactController::class, 'store']);

// Toptan Siparişler
Route::post('/toptan-siparisler', [WholesaleOrderController::class, 'store']);
