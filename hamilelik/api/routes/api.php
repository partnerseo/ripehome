<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BellyPhotoController;
use App\Http\Controllers\Api\ChecklistController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\PregnancyController;
use App\Http\Controllers\Api\ScreeningTemplateController;
use App\Http\Controllers\Api\ShareController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\WeekContentController;
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
        Route::post('consents', [AccountController::class, 'consent']);
        Route::get('me/export', [AccountController::class, 'exportData']);
        Route::delete('me', [AccountController::class, 'destroy']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('pregnancies/current', [PregnancyController::class, 'current']);
        Route::post('pregnancies', [PregnancyController::class, 'store']);
        Route::post('pregnancies/{pregnancy}/redate', [PregnancyController::class, 'redate']);
        Route::post('pregnancies/{pregnancy}/end', [PregnancyController::class, 'end']);

        // İçerik: tek hafta, ve çevrimdışı ön yükleme için tüm haftalar.
        Route::get('weeks', [WeekContentController::class, 'index']);
        Route::get('weeks/{week}', [WeekContentController::class, 'show'])->whereNumber('week');
        Route::get('screenings', [ScreeningTemplateController::class, 'index']);

        Route::get('appointments', [AppointmentController::class, 'index']);
        Route::post('appointments', [AppointmentController::class, 'store']);
        Route::patch('appointments/{appointment}', [AppointmentController::class, 'update']);
        Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy']);

        // Çevrimdışı yazılan her şey tek kapıdan girer.
        Route::post('sync', SyncController::class);

        Route::get('logs/health', [TrackingController::class, 'health']);
        Route::get('logs/symptoms', [TrackingController::class, 'symptoms']);
        Route::get('kick-sessions', [TrackingController::class, 'kicks']);
        Route::get('contraction-sessions', [TrackingController::class, 'contractions']);

        // Eşin salt-okunur erişimi.
        Route::get('shares', [ShareController::class, 'index']);
        Route::post('shares', [ShareController::class, 'store']);
        Route::post('shares/accept', [ShareController::class, 'accept']);
        Route::delete('shares/{share}', [ShareController::class, 'destroy']);
        Route::get('shared-pregnancies', [ShareController::class, 'sharedWithMe']);

        Route::get('belly-photos', [BellyPhotoController::class, 'index']);
        Route::post('belly-photos', [BellyPhotoController::class, 'store']);
        Route::get('belly-photos/{bellyPhoto}/file', [BellyPhotoController::class, 'show']);
        Route::delete('belly-photos/{bellyPhoto}', [BellyPhotoController::class, 'destroy']);

        Route::get('checklist', [ChecklistController::class, 'index']);
        Route::post('checklist', [ChecklistController::class, 'store']);
        Route::patch('checklist/{checklistItem}', [ChecklistController::class, 'update']);
        Route::delete('checklist/{checklistItem}', [ChecklistController::class, 'destroy']);

        Route::post('devices', [DeviceController::class, 'store']);
        Route::delete('devices', [DeviceController::class, 'destroy']);
    });
});
