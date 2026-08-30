<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PregnancyController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Kimlik uçları kaba kuvvete açık: servis içindeki e-posta/IP sınırlarına ek
    // olarak rota seviyesinde de sınırlanır.
    Route::middleware('throttle:6,1')->group(function () {
        Route::post('auth/otp/request', [AuthController::class, 'requestCode']);
        Route::post('auth/otp/verify', [AuthController::class, 'verify']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('pregnancies/current', [PregnancyController::class, 'current']);
        Route::post('pregnancies', [PregnancyController::class, 'store']);
        Route::post('pregnancies/{pregnancy}/redate', [PregnancyController::class, 'redate']);
        Route::post('pregnancies/{pregnancy}/end', [PregnancyController::class, 'end']);
    });
});
