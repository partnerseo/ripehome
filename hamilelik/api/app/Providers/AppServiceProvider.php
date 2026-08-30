<?php

namespace App\Providers;

use App\Models\ScreeningTemplate;
use App\Models\WeekContent;
use App\Services\Push\ExpoPushSender;
use App\Services\Push\PushSender;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PushSender::class, ExpoPushSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Onay bekleyen kayıt sayısı yan menüde her sayfada görünür.
        View::composer('admin.layout', function ($view): void {
            $view->with('pendingReview',
                WeekContent::query()->where('status', '!=', WeekContent::STATUS_PUBLISHED)->count()
                + ScreeningTemplate::query()->where('status', '!=', ScreeningTemplate::STATUS_PUBLISHED)->count(),
            );
        });
    }
}
