<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Category;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BulkProductImport extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'Toplu Ürün Yükle';
    protected static ?string $title = 'Toplu Ürün Yükleme';
    protected static ?string $navigationGroup = 'İçerik Yönetimi';
    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.bulk-product-import';

    // Global settings
    public ?string $categoryId = null;
    public bool $autoPublish = true;

    // Products array: each item has name, description, sku, featured, images (stored paths)
    public array $products = [];

    // Temporary file uploads keyed by product index
    public array $fileUploads = [];

    // Upload loading states
    public array $uploading = [];

    public function mount(): void
    {
        $this->initProducts(10);
    }

    protected function initProducts(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->products[] = [
                'name' => '',
                'description' => '',
                'sku' => '',
                'featured' => false,
                'images' => [],
            ];
        }
    }

    public function addMoreProducts(): void
    {
        $this->initProducts(5);
    }

    public function removeProduct(int $index): void
    {
        // Delete any uploaded images for this product
        if (!empty($this->products[$index]['images'])) {
            foreach ($this->products[$index]['images'] as $path) {
                \Storage::disk('public')->delete($path);
            }
        }

        unset($this->products[$index]);
        unset($this->fileUploads[$index]);
        unset($this->uploading[$index]);

        // Reindex
        $this->products = array_values($this->products);
        $this->fileUploads = [];
        $this->uploading = [];
    }

    public function removeImage(int $productIndex, int $imageIndex): void
    {
        if (isset($this->products[$productIndex]['images'][$imageIndex])) {
            $path = $this->products[$productIndex]['images'][$imageIndex];
            \Storage::disk('public')->delete($path);
            unset($this->products[$productIndex]['images'][$imageIndex]);
            $this->products[$productIndex]['images'] = array_values($this->products[$productIndex]['images']);
        }
    }

    public function updatedFileUploads($value, string $key): void
    {
        // key format: "0", "1", etc. (the product index)
        $index = (int) explode('.', $key)[0];

        $this->uploading[$index] = true;

        $files = $this->fileUploads[$index] ?? [];
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                try {
                    $path = $file->store('product-images', 'public');
                    if ($path) {
                        $this->products[$index]['images'][] = $path;
                    }
                } catch (\Throwable $e) {
                    \Log::error("Görsel yükleme hatası (index: {$index}): " . $e->getMessage());
                }
            }
        }

        // Clear the temp upload slot
        $this->fileUploads[$index] = null;
        $this->uploading[$index] = false;
    }

    public function save(): void
    {
        if (empty($this->categoryId)) {
            Notification::make()
                ->title('Kategori seçiniz')
                ->body('Lütfen ürünlerin ekleneceği bir kategori seçin.')
                ->danger()
                ->send();
            return;
        }

        $category = Category::find($this->categoryId);
        if (!$category) {
            Notification::make()
                ->title('Kategori bulunamadı')
                ->danger()
                ->send();
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($this->products as $productData) {
            $name = trim($productData['name'] ?? '');

            // Skip empty named products
            if (empty($name)) {
                $skipped++;
                continue;
            }

            try {
                // Generate unique slug
                $baseSlug = Str::slug($name);
                if (empty($baseSlug)) {
                    $baseSlug = 'urun-' . Str::random(6);
                }
                $slug = $baseSlug;
                $counter = 1;
                while (Product::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                // Handle SKU uniqueness
                $sku = trim($productData['sku'] ?? '');
                if (!empty($sku) && Product::where('sku', $sku)->exists()) {
                    $sku = $sku . '-' . Str::random(3);
                }

                Product::create([
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $productData['description'] ?? '',
                    'sku' => $sku ?: null,
                    'images' => $productData['images'] ?? [],
                    'category_id' => (int) $this->categoryId,
                    'is_active' => $this->autoPublish,
                    'is_featured' => $productData['featured'] ?? false,
                    'order' => $created,
                ]);

                $created++;
            } catch (\Throwable $e) {
                \Log::error("Toplu ürün kayıt hatası ({$name}): " . $e->getMessage());
                Notification::make()
                    ->title("Hata: {$name}")
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        }

        if ($created > 0) {
            Notification::make()
                ->title('Toplu yükleme tamamlandı')
                ->body("{$created} ürün başarıyla oluşturuldu." . ($skipped > 0 ? " {$skipped} boş kart atlandı." : ''))
                ->success()
                ->duration(8000)
                ->send();

            // Reset form
            $this->products = [];
            $this->fileUploads = [];
            $this->uploading = [];
            $this->initProducts(10);
        } else {
            Notification::make()
                ->title('Kaydedilecek ürün yok')
                ->body('Lütfen en az bir ürüne isim girin.')
                ->warning()
                ->send();
        }
    }

    public function getFilledCountProperty(): int
    {
        return collect($this->products)->filter(fn ($p) => !empty(trim($p['name'] ?? '')))->count();
    }

    public function getTotalImagesProperty(): int
    {
        return collect($this->products)->sum(fn ($p) => count($p['images'] ?? []));
    }

    public function getCategoriesProperty(): \Illuminate\Support\Collection
    {
        return Category::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }
}
