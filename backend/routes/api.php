<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FeaturedProductController;
use App\Http\Controllers\Api\FeaturedSectionController;
use App\Http\Controllers\Api\HomeSliderController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\WholesaleOrderController;
use Illuminate\Support\Facades\Route;

// Health check (sadece app durumu, DB bilgisi yok)
Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
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

// ─── Public GET — okuma endpointleri (throttle: 120/dakika) ──────────────────
Route::middleware('throttle:120,1')->group(function () {
    Route::get('/kategoriler',                                [CategoryController::class,       'index']);
    Route::get('/kategoriler/{slug}',                        [CategoryController::class,       'show']);
    Route::get('/urunler',                                   [ProductController::class,        'index']);
    Route::get('/urunler/{slug}',                            [ProductController::class,        'show']);
    Route::get('/urunler/kategori/{category_slug}',          [ProductController::class,        'byCategory']);
    Route::get('/sayfalar/{slug}',                           [PageController::class,           'show']);
    Route::get('/anasayfa-slider',                           [HomeSliderController::class,     'index']);
    Route::get('/one-cikan-bolumler',                        [FeaturedSectionController::class,'index']);
    Route::get('/one-cikan-urunler',                         [FeaturedProductController::class,'index']);
    Route::get('/ayarlar',                                   [SettingController::class,        'index']);
    Route::get('/blog',                                      [BlogPostController::class,       'index']);
    Route::get('/blog/{slug}',                               [BlogPostController::class,       'show']);
    Route::get('/yorumlar',                                  [TestimonialController::class,    'index']);
});

// ─── İletişim formu (throttle: 10/saat — spam koruması) ──────────────────────
Route::post('/iletisim', [ContactController::class, 'store'])->middleware('throttle:10,60');

// ─── Toptan Sipariş (üye girişi zorunlu + throttle: 20/gün) ──────────────────
Route::post('/toptan-siparisler', [WholesaleOrderController::class, 'store'])
    ->middleware(['auth.member', 'throttle:10,60']);

// ─── Üye siparişleri ──────────────────────────────────────────────────────────
Route::get('/uye/siparislerim', [WholesaleOrderController::class, 'myOrders'])
    ->middleware('auth.member');

// ─── Üye Auth ─────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('send-otp',   [AuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('register',   [AuthController::class, 'register']);

    Route::middleware('auth.member')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',      [AuthController::class, 'me']);
    });
});
