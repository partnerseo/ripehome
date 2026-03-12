<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\LoginLog;
use App\Models\WholesaleOrder;
use Filament\Pages\Page;
use Filament\Actions\Action;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Genel Bakış';
    protected static ?string $title = 'Genel Bakış';
    protected static ?int $navigationSort = -2;
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $slug = '';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('visit_site')
                ->label('Siteyi Görüntüle')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url('https://ripehome.com.tr', shouldOpenInNewTab: true)
                ->color('gray'),
            Action::make('new_product')
                ->label('Yeni Ürün Ekle')
                ->icon('heroicon-o-plus')
                ->url('/admin/products/create')
                ->color('primary'),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'totalMessages' => ContactMessage::count(),
            'unreadMessages' => ContactMessage::where('is_read', false)->count(),
            'totalOrders' => WholesaleOrder::count(),
            'pendingOrders' => WholesaleOrder::where('status', 'pending')->count(),
            'recentOrders' => WholesaleOrder::latest()->take(5)->get(),
            'recentMessages' => ContactMessage::latest()->take(5)->get(),
            'topCategories' => Category::withCount('products')->orderByDesc('products_count')->take(5)->get(),
            'loginLogs' => LoginLog::with('user')->latest('logged_in_at')->take(10)->get(),
        ];
    }
}
