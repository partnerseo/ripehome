<?php

namespace App\Filament\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ImageAssignment extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Resim Atama';
    protected static ?string $title = 'Sahipsiz Resim Atama';
    protected static ?string $navigationGroup = 'İçerik Yönetimi';
    protected static ?int $navigationSort = 98;

    protected static string $view = 'filament.pages.image-assignment';

    public array $selectedImages = [];
    public string $productSearch = '';
    public string $imageSearch = '';
    public ?int $assignedCount = null;
    public string $activeTab = 'ata';
    public string $missingSearch = '';

    public function getOrphanImages(): array
    {
        $allFiles = Storage::disk('public')->files('product-images');

        $referenced = [];
        Product::whereNotNull('images')
            ->where('images', '!=', '[]')
            ->chunk(200, function ($products) use (&$referenced) {
                foreach ($products as $p) {
                    foreach (($p->images ?? []) as $img) {
                        if (str_contains($img, 'product-images/')) {
                            $referenced[] = $img;
                        }
                    }
                }
            });

        $referencedSet = array_flip($referenced);

        $orphans = [];
        foreach ($allFiles as $file) {
            if (!isset($referencedSet[$file])) {
                $orphans[] = $file;
            }
        }

        if ($this->imageSearch) {
            $q = strtolower($this->imageSearch);
            $orphans = array_filter($orphans, fn($f) => str_contains(strtolower($f), $q));
        }

        return array_values($orphans);
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->productSearch = '';
        $this->missingSearch = '';
    }

    public function getMissingProducts(): array
    {
        $q = Product::with('category')
            ->where(function ($q) {
                $q->whereNull('images')->orWhereRaw('JSON_LENGTH(images) = 0');
            })
            ->orderBy('category_id')
            ->orderBy('id');

        if ($this->missingSearch) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.tr')) LIKE ?", ['%' . $this->missingSearch . '%']);
        }

        return $q->get()->map(fn($p) => [
            'id'       => $p->id,
            'name'     => data_get(json_decode($p->name ?? '{}', true), 'tr', $p->name),
            'category' => $p->category?->name ?? '—',
            'edit_url' => '/admin/products/' . $p->id . '/edit',
        ])->toArray();
    }

    public function getEmptyProducts(): array
    {
        $q = Product::with('category')
            ->where(function ($q) {
                $q->whereNull('images')->orWhereRaw('JSON_LENGTH(images) = 0');
            })
            ->orderBy('category_id')
            ->orderBy('id');

        if ($this->productSearch) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.tr')) LIKE ?", ['%' . $this->productSearch . '%']);
        }

        return $q->get()->map(fn($p) => [
            'id'       => $p->id,
            'name'     => data_get(json_decode($p->name ?? '{}', true), 'tr', $p->name),
            'category' => $p->category?->name ?? '',
        ])->toArray();
    }

    public function toggleImage(string $file): void
    {
        if (in_array($file, $this->selectedImages)) {
            $this->selectedImages = array_values(array_filter($this->selectedImages, fn($f) => $f !== $file));
        } else {
            $this->selectedImages[] = $file;
        }
    }

    public function selectRange(string $from, string $to): void
    {
        $orphans = $this->getOrphanImages();
        $fromIdx = array_search($from, $orphans);
        $toIdx   = array_search($to, $orphans);
        if ($fromIdx === false || $toIdx === false) return;
        if ($fromIdx > $toIdx) [$fromIdx, $toIdx] = [$toIdx, $fromIdx];
        for ($i = $fromIdx; $i <= $toIdx; $i++) {
            if (!in_array($orphans[$i], $this->selectedImages)) {
                $this->selectedImages[] = $orphans[$i];
            }
        }
    }

    public function clearSelection(): void
    {
        $this->selectedImages = [];
    }

    public function assignToProduct(int $productId): void
    {
        if (empty($this->selectedImages)) {
            Notification::make()->title('Önce resim seçin')->warning()->send();
            return;
        }

        $product = Product::find($productId);
        if (!$product) {
            Notification::make()->title('Ürün bulunamadı')->danger()->send();
            return;
        }

        $existing = $product->images ?? [];
        $newImages = array_values(array_unique(array_merge($existing, $this->selectedImages)));
        $product->update(['images' => $newImages]);

        $count = count($this->selectedImages);
        $name  = data_get(json_decode($product->name ?? '{}', true), 'tr', $product->name);

        Notification::make()
            ->title("{$count} resim atandı → {$name}")
            ->success()
            ->send();

        $this->selectedImages = [];
        $this->assignedCount  = $count;
    }

    public function getAssignedProducts(): array
    {
        // Sadece bizim atadığımız resimler (product-images/ klasörü) olan ürünleri göster.
        // Eskiden zaten doğru resimleri olan ürünler (products/category/... yolu) gösterilmez.
        $q = Product::with('category')
            ->whereRaw("JSON_LENGTH(images) > 0")
            ->whereRaw("images LIKE '%product-images/%'")
            ->orderBy('category_id')
            ->orderBy('id');

        if ($this->productSearch) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.tr')) LIKE ?", ['%' . $this->productSearch . '%']);
        }

        return $q->get()->map(fn($p) => [
            'id'       => $p->id,
            'name'     => data_get(json_decode($p->name ?? '{}', true), 'tr', $p->name),
            'category' => $p->category?->name ?? '',
            'images'   => array_values(array_filter($p->images ?? [], fn($img) => str_contains($img, 'product-images/'))),
        ])->filter(fn($p) => count($p['images']) > 0)->values()->toArray();
    }

    public function removeImageFromProduct(int $productId, string $imagePath): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        $images = array_values(array_filter($product->images ?? [], fn($img) => $img !== $imagePath));
        $product->update(['images' => $images]);

        Notification::make()->title('Resim kaldırıldı')->success()->send();
    }
}
