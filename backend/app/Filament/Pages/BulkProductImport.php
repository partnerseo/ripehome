<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Category;
use App\Models\BulkImportDraft;
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

    // Products array
    public array $products = [];

    // Temporary file uploads keyed by product index
    public array $fileUploads = [];

    // Upload loading states
    public array $uploading = [];

    // Draft
    public ?int $activeDraftId = null;
    public string $draftName = '';
    public bool $showDraftModal = false;
    public bool $showLoadModal = false;

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
        if (!empty($this->products[$index]['images'])) {
            foreach ($this->products[$index]['images'] as $path) {
                \Storage::disk('public')->delete($path);
            }
        }

        unset($this->products[$index]);
        unset($this->fileUploads[$index]);
        unset($this->uploading[$index]);

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

        $this->fileUploads[$index] = null;
        $this->uploading[$index] = false;
    }

    // ==================== TASLAK İŞLEMLERİ ====================

    public function openSaveDraftModal(): void
    {
        if ($this->activeDraftId) {
            $draft = BulkImportDraft::find($this->activeDraftId);
            $this->draftName = $draft?->name ?? '';
        } else {
            $category = Category::find($this->categoryId);
            $this->draftName = ($category ? $category->name . ' - ' : '') . 'Taslak ' . now()->format('d.m H:i');
        }
        $this->showDraftModal = true;
    }

    public function saveDraft(): void
    {
        $name = trim($this->draftName);
        if (empty($name)) {
            $name = 'Taslak ' . now()->format('d.m.Y H:i');
        }

        $data = [
            'user_id' => auth()->id(),
            'name' => $name,
            'category_id' => $this->categoryId,
            'auto_publish' => $this->autoPublish,
            'products' => $this->products,
        ];

        if ($this->activeDraftId) {
            $draft = BulkImportDraft::find($this->activeDraftId);
            if ($draft) {
                $draft->update($data);
            }
        } else {
            $draft = BulkImportDraft::create($data);
            $this->activeDraftId = $draft->id;
        }

        $this->showDraftModal = false;

        Notification::make()
            ->title('Taslak kaydedildi')
            ->body("\"{$name}\" başarıyla kaydedildi.")
            ->success()
            ->send();
    }

    public function quickSaveDraft(): void
    {
        if ($this->activeDraftId) {
            $draft = BulkImportDraft::find($this->activeDraftId);
            if ($draft) {
                $draft->update([
                    'category_id' => $this->categoryId,
                    'auto_publish' => $this->autoPublish,
                    'products' => $this->products,
                ]);

                Notification::make()
                    ->title('Taslak güncellendi')
                    ->success()
                    ->duration(2000)
                    ->send();
                return;
            }
        }

        $this->openSaveDraftModal();
    }

    public function openLoadModal(): void
    {
        $this->showLoadModal = true;
    }

    public function loadDraft(int $draftId): void
    {
        $draft = BulkImportDraft::where('user_id', auth()->id())->find($draftId);
        if (!$draft) {
            Notification::make()->title('Taslak bulunamadı')->danger()->send();
            return;
        }

        $this->categoryId = $draft->category_id;
        $this->autoPublish = $draft->auto_publish;
        $this->products = $draft->products;
        $this->activeDraftId = $draft->id;
        $this->fileUploads = [];
        $this->uploading = [];
        $this->showLoadModal = false;

        Notification::make()
            ->title('Taslak yüklendi')
            ->body("\"{$draft->name}\" yüklendi. {$this->filledCount} ürün, {$this->totalImages} görsel.")
            ->success()
            ->send();
    }

    public function deleteDraft(int $draftId): void
    {
        $draft = BulkImportDraft::where('user_id', auth()->id())->find($draftId);
        if ($draft) {
            $name = $draft->name;
            $draft->delete();

            if ($this->activeDraftId === $draftId) {
                $this->activeDraftId = null;
            }

            Notification::make()
                ->title('Taslak silindi')
                ->body("\"{$name}\" silindi.")
                ->success()
                ->send();
        }
    }

    public function getDraftsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return BulkImportDraft::where('user_id', auth()->id())
            ->orderByDesc('updated_at')
            ->get();
    }

    // ==================== KAYDETME ====================

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
            Notification::make()->title('Kategori bulunamadı')->danger()->send();
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($this->products as $productData) {
            $name = trim($productData['name'] ?? '');
            if (empty($name)) {
                $skipped++;
                continue;
            }

            try {
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
            if ($this->activeDraftId) {
                BulkImportDraft::where('id', $this->activeDraftId)->delete();
                $this->activeDraftId = null;
            }

            Notification::make()
                ->title('Toplu yükleme tamamlandı')
                ->body("{$created} ürün başarıyla oluşturuldu." . ($skipped > 0 ? " {$skipped} boş kart atlandı." : ''))
                ->success()
                ->duration(8000)
                ->send();

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
