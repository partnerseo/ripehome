<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ScreeningTemplateController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\WeekContentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('giris', [SessionController::class, 'create'])->name('login');
        Route::post('giris', [SessionController::class, 'store'])->middleware('throttle:10,1');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('cikis', [SessionController::class, 'destroy'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');

        // Hekimin gözden geçirme kuyruğu: onay bekleyenleri okumak ve
        // onaylamak için ayrı bir akış. Editörün düzenleme formundan farklı.
        Route::get('onay', [ReviewController::class, 'index'])->name('review.index');
        Route::get('onay/{type}/{id}', [ReviewController::class, 'show'])
            ->whereIn('type', ['hafta', 'tetkik'])->whereNumber('id')->name('review.show');
        Route::post('onay/{type}/{id}', [ReviewController::class, 'approve'])
            ->whereIn('type', ['hafta', 'tetkik'])->whereNumber('id')->name('review.approve');

        Route::get('haftalar', [WeekContentController::class, 'index'])->name('weeks.index');
        Route::get('haftalar/yeni', [WeekContentController::class, 'create'])->name('weeks.create');
        Route::post('haftalar', [WeekContentController::class, 'store'])->name('weeks.store');
        Route::get('haftalar/{weekContent}', [WeekContentController::class, 'edit'])->name('weeks.edit');
        Route::put('haftalar/{weekContent}', [WeekContentController::class, 'update'])->name('weeks.update');
        Route::delete('haftalar/{weekContent}', [WeekContentController::class, 'destroy'])->name('weeks.destroy');

        Route::get('tetkikler', [ScreeningTemplateController::class, 'index'])->name('screenings.index');
        Route::get('tetkikler/yeni', [ScreeningTemplateController::class, 'create'])->name('screenings.create');
        Route::post('tetkikler', [ScreeningTemplateController::class, 'store'])->name('screenings.store');
        Route::get('tetkikler/{screeningTemplate}', [ScreeningTemplateController::class, 'edit'])->name('screenings.edit');
        Route::put('tetkikler/{screeningTemplate}', [ScreeningTemplateController::class, 'update'])->name('screenings.update');
    });
});
