<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportFromStorageSeeder extends Seeder
{
    /**
     * Storage'daki ürün klasörlerinden kategorileri ve ürünleri oluştur
     */
    public function run(): void
    {
        $storagePath = storage_path('app/public/products');
        
        if (!File::exists($storagePath)) {
            $this->command->error('❌ Storage/products klasörü bulunamadı!');
            return;
        }
        
        $this->command->info('📂 Storage klasörleri taranıyor...\n');
        
        $stats = ['categories' => 0, 'products' => 0];
        
        // Ana klasörleri tara (her klasör = 1 kategori)
        $mainFolders = File::directories($storagePath);
        
        if (empty($mainFolders)) {
            $this->command->error('❌ Ürün klasörü bulunamadı!');
            return;
        }
        
        $this->command->info('📦 ' . count($mainFolders) . ' ana klasör bulundu\n');
        
        foreach ($mainFolders as $mainFolder) {
            $categoryName = $this->formatCategoryName(basename($mainFolder));
            
            $this->command->info("📁 Kategori: {$categoryName}");
            
            // Kategori oluştur
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => $this->getCategoryDescription($categoryName),
                    'is_active' => true,
                    'order' => Category::max('order') + 1,
                ]
            );
            
            if ($category->wasRecentlyCreated) {
                $stats['categories']++;
                $this->command->info("   ✅ Kategori oluşturuldu");
            } else {
                $this->command->info("   ℹ️  Kategori mevcut");
            }
            
            // Tüm alt klasörlerdeki görselleri bul (rekursif)
            $allImages = $this->getAllImagesRecursively($mainFolder);
            
            $this->command->info("   🖼️  Toplam " . count($allImages) . " görsel bulundu");
            
            if (empty($allImages)) {
                $this->command->warn("   ⚠️  Görsel yok, atlanıyor\n");
                continue;
            }
            
            // Görselleri grupla (renk/varyasyona göre)
            $groupedImages = $this->groupImagesBySubfolder($mainFolder, $allImages);
            
            // Her grup için ürün oluştur
            $productCount = 0;
            foreach ($groupedImages as $subfolderName => $images) {
                $colorName = $this->extractColorName($subfolderName);
                
                $this->command->info("      🎨 {$colorName}: " . count($images) . " görsel");
                
                // İlk 3 görseli ürün olarak ekle
                $imageLimit = min(3, count($images));
                for ($i = 0; $i < $imageLimit; $i++) {
                    $imagePath = $images[$i];
                    $this->createProduct(
                        $imagePath,
                        $category,
                        $categoryName,
                        $colorName,
                        $i
                    );
                    
                    $productCount++;
                    $stats['products']++;
                }
            }
            
            $this->command->info("   ✅ {$productCount} ürün eklendi\n");
        }
        
        $this->command->info("\n════════════════════════════════════════");
        $this->command->info("✅ İŞLEM TAMAMLANDI!");
        $this->command->info("════════════════════════════════════════");
        $this->command->info("📊 Oluşturulan kategoriler: {$stats['categories']}");
        $this->command->info("📊 Oluşturulan ürünler: {$stats['products']}");
        $this->command->info("════════════════════════════════════════\n");
    }
    
    /**
     * Klasör adını düzenle
     */
    private function formatCategoryName(string $folderName): string
    {
        // "2-kat-batik-bornoz" -> "2 Kat Batik Bornoz"
        $name = str_replace('-', ' ', $folderName);
        $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
        return $name;
    }
    
    /**
     * Alt klasörlerdeki tüm görselleri bul (rekursif)
     */
    private function getAllImagesRecursively(string $directory): array
    {
        $images = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $images[] = $file->getPathname();
                }
            }
        }
        
        return $images;
    }
    
    /**
     * Görselleri alt klasörlere göre grupla
     */
    private function groupImagesBySubfolder(string $mainFolder, array $images): array
    {
        $grouped = [];
        
        foreach ($images as $imagePath) {
            // Alt klasör adını al
            $relativePath = str_replace($mainFolder . '/', '', dirname($imagePath));
            
            // Ana klasördeki görseller için "genel" grubu
            if (empty($relativePath) || $relativePath === '.') {
                $relativePath = 'Standart';
            }
            
            if (!isset($grouped[$relativePath])) {
                $grouped[$relativePath] = [];
            }
            
            $grouped[$relativePath][] = $imagePath;
        }
        
        return $grouped;
    }
    
    /**
     * Alt klasör adından renk/varyasyon adını çıkar
     */
    private function extractColorName(string $subfolderName): string
    {
        // "acik-gri" -> "Açık Gri"
        // "batik-bornoz-mavi" -> "Mavi"
        
        if ($subfolderName === 'Standart') {
            return 'Standart';
        }
        
        // Son kelimeyi renk olarak al
        $parts = explode('/', $subfolderName);
        $lastPart = end($parts);
        
        $name = str_replace('-', ' ', $lastPart);
        $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
        
        return $name;
    }
    
    /**
     * Ürün oluştur
     */
    private function createProduct(
        string $imagePath,
        Category $category,
        string $categoryName,
        string $colorName,
        int $index
    ): void {
        // Görsel yolunu storage'a göre düzenle
        // /path/to/storage/app/public/products/xxx.jpg -> products/xxx.jpg
        $storagePath = storage_path('app/public/');
        $relativePath = str_replace($storagePath, '', $imagePath);
        
        $productName = $categoryName . ' - ' . $colorName;
        if ($index > 0) {
            $productName .= ' (' . ($index + 1) . ')';
        }
        
        $slug = Str::slug($productName . '-' . Str::random(6));
        
        // Ürün zaten var mı kontrol et (aynı görsel)
        $existingProduct = Product::where('images', 'like', '%' . basename($relativePath) . '%')->first();
        
        if ($existingProduct) {
            return; // Zaten var, atla
        }
        
        Product::create([
            'category_id' => $category->id,
            'name' => $productName,
            'slug' => $slug,
            'description' => $this->getProductDescription($categoryName, $colorName),
            'images' => json_encode([$relativePath]),
            'is_active' => true,
            'is_featured' => false,
            'order' => 0,
        ]);
    }
    
    /**
     * Kategori açıklaması oluştur
     */
    private function getCategoryDescription(string $categoryName): string
    {
        $descriptions = [
            'bornoz' => 'Yumuşak ve emici bornoz modellerimiz. Lüks banyo deneyimi için ideal.',
            'kimono' => 'Şık ve rahat kimono bornoz çeşitleri. Farklı renk seçenekleri.',
            'muslin' => 'Hafif ve nefes alabilir muslin ürünler. Bebek ve çocuk için ideal.',
            'havlu' => 'Kaliteli pamuklu havlu seçeneklerimiz. Banyo ve plaj için.',
            'nevresim' => 'Konforlu uyku için şık nevresim takımları.',
            'set' => 'Özel tasarım ev tekstili setleri. Hediye için mükemmel.',
        ];
        
        $lowerName = mb_strtolower($categoryName);
        
        foreach ($descriptions as $key => $desc) {
            if (str_contains($lowerName, $key)) {
                return $desc;
            }
        }
        
        return "Premium kalite {$categoryName} ürünlerimiz.";
    }
    
    /**
     * Ürün açıklaması oluştur
     */
    private function getProductDescription(string $categoryName, string $colorName): string
    {
        return "Premium kalite {$categoryName}, {$colorName} renk seçeneği. " .
               "Yüksek kaliteli malzemeden üretilmiş, uzun ömürlü kullanım için ideal. " .
               "Ripe Home güvencesiyle.";
    }
    
    /**
     * Rastgele fiyat oluştur
     */
    private function generatePrice(): float
    {
        $prices = [299.99, 399.99, 499.99, 599.99, 699.99, 799.99, 899.99, 999.99];
        return $prices[array_rand($prices)];
    }
}

