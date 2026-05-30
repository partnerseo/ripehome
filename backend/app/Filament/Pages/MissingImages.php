<?php

namespace App\Filament\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class MissingImages extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?string $navigationLabel = 'Resimsiz Ürünler';
    protected static ?string $title = 'Resimsiz Ürünler';
    protected static ?string $navigationGroup = 'İçerik Yönetimi';
    protected static ?int $navigationSort = 97;

    protected static string $view = 'filament.pages.missing-images';

    public string $search = '';

    public function getProducts(): array
    {
        $q = Product::with('category')
            ->where(function ($q) {
                $q->whereNull('images')->orWhereRaw('JSON_LENGTH(images) = 0');
            })
            ->orderBy('category_id')
            ->orderBy('id');

        if ($this->search) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.tr')) LIKE ?", ['%' . $this->search . '%']);
        }

        return $q->get()->map(fn($p) => [
            'id'       => $p->id,
            'name'     => data_get(json_decode($p->name ?? '{}', true), 'tr', $p->name),
            'category' => $p->category?->name ?? '—',
            'edit_url' => '/admin/products/' . $p->id . '/edit',
        ])->toArray();
    }

    public function getTotalCount(): int
    {
        return Product::where(function ($q) {
            $q->whereNull('images')->orWhereRaw('JSON_LENGTH(images) = 0');
        })->count();
    }

    public function check(int $productId): void
    {
        $product = Product::find($productId);

        if ($product && !empty($product->images)) {
            Notification::make()
                ->title('Resim yüklendi, listeden kaldırıldı ✓')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Henüz resim yüklenmemiş')
                ->warning()
                ->body('Ürün düzenleme sayfasında resim yükleyip kaydedin.')
                ->send();
        }
    }

    public function refresh(): void
    {
        // Livewire sayfayı yeniden render eder, getProducts() güncel listeyi döner
    }
}
