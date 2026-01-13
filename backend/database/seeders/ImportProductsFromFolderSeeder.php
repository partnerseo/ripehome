<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ImportProductsFromFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $basePath = base_path('../public/ÜRÜNLER-temiz');
        
        if (!File::exists($basePath)) {
            $this->command->error('❌ Klasör bulunamadı: ' . $basePath);
            $this->command->info('ℹ️  Önce "npm run prepare-products" komutunu çalıştırın!');
            return;
        }
        
        $this->command->info('📂 Ana klasörler taranıyor...\n');
        
        $stats = ['categories' => 0, 'products' => 0];
        
        // ANA KLASÖRLERİ TARA (sadece 1. seviye)
        $mainFolders = File::directories($basePath);
        
        if (empty($mainFolders)) {
            $this->command->error('❌ Ana klasör bulunamadı!');
            return;
        }
        
        $this->command->info('📦 ' . count($mainFolders) . ' ana klasör bulundu\n');
        
        foreach ($mainFolders as $mainFolder) {
            // Kategori adı = klasör adı (AYNEN, değişiklik yok!)
            $categoryName = basename($mainFolder);
            
            $this->command->info("📁 Kategori: {$categoryName}");
            
            // Kategori oluştur (sadece ana klasörden)
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
            
            // TÜM ALT KLASÖRLERDEKİ GÖRSELLERİ BUL (rekursif)
            $allImages = $this->getAllImagesRecursively($mainFolder);
            
            $this->command->info("   🖼️  Toplam " . count($allImages) . " görsel bulundu");
            
            if (empty($allImages)) {
                $this->command->warn("   ⚠️  Görsel yok, atlanıyor\n");
                continue;
            }
            
            // Görselleri grupla (alt klasör adına göre)
            $groupedImages = $this->groupImagesBySubfolder($mainFolder, $allImages);
            
            // Her grup için ürün oluştur
            $productCount = 0;
            foreach ($groupedImages as $subfolderName => $images) {
                $colorName = $this->extractColorName($subfolderName);
                
                $this->command->info("      🎨 {$colorName}: " . count($images) . " görsel");
                
                foreach ($images as $index => $imagePath) {
                    $this->createProduct(
                        $imagePath,
                        $category,
                        $categoryName,
                        $colorName,
                        $index
                    );
                    
                    $productCount++;
                    $stats['products']++;
                }
            }
            
            $this->command->info("   ✅ {$productCount} ürün eklendi\n");
        }
        
        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✨ Tamamlandı!');
        $this->command->info("📦 Kategori: {$stats['categories']} yeni");
        $this->command->info("🖼️  Ürün: {$stats['products']}");
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');
        $this->command->info('🌐 Kontrol edin:');
        $this->command->info('   http://localhost:8000/admin/categories');
        $this->command->info('   http://localhost:8000/admin/products');
    }
    
    /**
     * Tüm görselleri rekursif olarak bul
     */
    private function getAllImagesRecursively($directory): array
    {
        $images = [];
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $images[] = $file->getPathname();
                }
            }
        }
        
        return $images;
    }
    
    /**
     * Görselleri alt klasöre göre grupla
     */
    private function groupImagesBySubfolder($mainFolder, $images): array
    {
        $grouped = [];
        
        foreach ($images as $imagePath) {
            $relativePath = str_replace($mainFolder . DIRECTORY_SEPARATOR, '', $imagePath);
            $parts = explode(DIRECTORY_SEPARATOR, $relativePath);
            
            // Alt klasör adını al (ilk klasör renk/varyant)
            $subfolder = count($parts) > 1 ? $parts[0] : 'genel';
            
            if (!isset($grouped[$subfolder])) {
                $grouped[$subfolder] = [];
            }
            
            $grouped[$subfolder][] = $imagePath;
        }
        
        return $grouped;
    }
    
    /**
     * Alt klasör adından renk çıkar
     * "BATİK BORNOZ ANTRASİT" → "ANTRASİT"
     */
    private function extractColorName($subfolderName): string
    {
        $name = strtoupper($subfolderName);
        
        // Bilinen renkler
        $colors = [
            'ANTRASİT', 'ANTRASIT',
            'MAVİ', 'MAVI',
            'PEMBE',
            'BEJ',
            'SİYAH', 'SIYAH',
            'BEYAZ',
            'YEŞİL', 'YESIL',
            'KIRMIZI',
            'SARI',
            'MOR',
            'TURUNCU',
            'GRİ', 'GRI',
            'AÇIK GRİ', 'ACIK GRI',
            'KOYU GRİ', 'KOYU GRI',
            'LACİVERT', 'LACIVERT',
            'KAHVERENGİ', 'KAHVERENGI',
            'VİZON', 'VIZON',
            'KREM',
            'PETROL',
            'TURKUAZ',
            'MİNT', 'MINT',
            'HAKİ', 'HAKI',
            'FUSYA',
            'GOLD',
            'HARDAL',
        ];
        
        // Renk ismi ara
        foreach ($colors as $color) {
            if (str_contains($name, $color)) {
                return ucwords(strtolower($color));
            }
        }
        
        // Renk bulunamazsa son kelimeyi kullan
        $words = preg_split('/\s+/', trim($name));
        if (count($words) > 0) {
            return ucwords(strtolower(end($words)));
        }
        
        return 'Genel';
    }
    
    /**
     * Ürün oluştur
     */
    private function createProduct($imagePath, $category, $categoryName, $colorName, $index): void
    {
        // Backend'e kopyala
        $destFolder = "products/" . Str::slug($category->slug);
        $destPath = storage_path("app/public/{$destFolder}");
        
        if (!File::exists($destPath)) {
            File::makeDirectory($destPath, 0755, true);
        }
        
        $filename = pathinfo($imagePath, PATHINFO_FILENAME);
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $newFilename = Str::slug($colorName . '-' . $filename) . '-' . uniqid() . '.' . $extension;
        
        $destinationFile = "{$destPath}/{$newFilename}";
        
        // Dosyayı kopyala
        if (!File::exists($destinationFile)) {
            File::copy($imagePath, $destinationFile);
        }
        
        // Ürün adı: "Kategori - Renk - No"
        $productName = "{$categoryName} - {$colorName} - " . ($index + 1);
        
        // Slug oluştur
        $slug = Str::slug($productName) . '-' . uniqid();
        
        // Zaten varsa atla
        if (Product::where('slug', $slug)->exists()) {
            return;
        }
        
        // Açıklama
        $description = $this->generateProductDescription($categoryName, $colorName);
        
        // Renk tag'i oluştur
        $colorTag = Tag::firstOrCreate(
            ['name' => $colorName],
            [
                'color' => $this->getColorHex($colorName),
                'is_active' => true,
            ]
        );
        
        // Ürün oluştur
        $product = Product::create([
            'name' => $productName,
            'slug' => $slug,
            'description' => $description,
            'images' => ["{$destFolder}/{$newFilename}"],
            'category_id' => $category->id,
            'features' => [
                ['icon' => 'check', 'title' => 'Premium Kalite', 'description' => 'Yüksek kalite standartları'],
                ['icon' => 'truck', 'title' => 'Hızlı Kargo', 'description' => 'Aynı gün kargoya verilir'],
                ['icon' => 'package', 'title' => 'Toptan Uygun', 'description' => 'Toptan sipariş için ideal'],
            ],
            'meta_title' => $productName,
            'meta_description' => strip_tags($description),
            'is_active' => true,
            'is_featured' => rand(1, 100) <= 20, // %20 şans
            'order' => Product::where('category_id', $category->id)->max('order') + 1,
        ]);
        
        // Tag ekle
        $product->tags()->attach($colorTag->id);
    }
    
    /**
     * Kategori açıklaması
     */
    private function getCategoryDescription($categoryName): string
    {
        $lowerName = strtolower($categoryName);
        
        $descriptions = [
            'batik' => 'Özel batik desenli, şık ve modern ev tekstili ürünleri.',
            'jakar' => 'Jakar dokuma tekniği ile üretilen kaliteli ürünler.',
            'kimono' => 'Rahat ve şık kimono modelleri.',
            'müslin' => 'Doğal ve yumuşak müslin kumaştan üretilmiş ürünler.',
            'bornoz' => 'Konforlu ve emici bornoz çeşitleri.',
            'havlu' => 'Yumuşak ve emici havlu ürünleri.',
            'natural' => 'Doğal ve organik malzemelerden üretilmiş koleksiyon.',
            'renkli' => 'Canlı renk seçenekleri ile modern tasarımlar.',
            'şerit' => 'Şık şerit detaylı ürünler.',
            'viskon' => 'Premium viskon kumaştan üretilen lüks ürünler.',
            'waffle' => 'Özel waffle dokuma ürünler.',
            'set' => 'Uyumlu takım ve set ürünleri.',
            'çocuk' => 'Çocuklar için özel tasarlanmış koleksiyon.',
            'panço' => 'Pratik ve şık panço modelleri.',
        ];
        
        foreach ($descriptions as $keyword => $description) {
            if (str_contains($lowerName, $keyword)) {
                return $description;
            }
        }
        
        return "Özenle seçilmiş {$categoryName} ürünleri.";
    }
    
    /**
     * Ürün açıklaması oluştur
     */
    private function generateProductDescription($categoryName, $colorName): string
    {
        $baseDesc = "Yüksek kaliteli malzemeden üretilmiş, konforlu ve şık tasarım.";
        $colorInfo = "{$colorName} renk seçeneği ile zarif görünüm.";
        
        return "<p>Kaliteli {$categoryName} ürünü. {$colorInfo}</p><p>{$baseDesc}</p><p>Toptan satış için idealdir. %100 kaliteli kumaş, uzun ömürlü kullanım.</p>";
    }
    
    /**
     * Renk hex kodu
     */
    private function getColorHex($colorName): string
    {
        $lowerColor = strtolower($colorName);
        
        return match(true) {
            str_contains($lowerColor, 'acik') && str_contains($lowerColor, 'gri') => '#D1D5DB',
            str_contains($lowerColor, 'koyu') && str_contains($lowerColor, 'gri') => '#6B7280',
            str_contains($lowerColor, 'gri') => '#9CA3AF',
            str_contains($lowerColor, 'bej') => '#F5F5DC',
            str_contains($lowerColor, 'siyah') => '#000000',
            str_contains($lowerColor, 'beyaz') => '#FFFFFF',
            str_contains($lowerColor, 'mavi') => '#3B82F6',
            str_contains($lowerColor, 'kirmizi') => '#EF4444',
            str_contains($lowerColor, 'yesil') => '#10B981',
            str_contains($lowerColor, 'sari') => '#F59E0B',
            str_contains($lowerColor, 'turuncu') => '#F97316',
            str_contains($lowerColor, 'pembe') => '#EC4899',
            str_contains($lowerColor, 'mor') => '#8B5CF6',
            str_contains($lowerColor, 'lila') => '#A78BFA',
            str_contains($lowerColor, 'kahverengi') => '#92400E',
            str_contains($lowerColor, 'vizon') => '#C2B280',
            str_contains($lowerColor, 'antrasit') => '#293241',
            str_contains($lowerColor, 'krem') => '#FFF8DC',
            str_contains($lowerColor, 'petrol') => '#006D77',
            str_contains($lowerColor, 'turkuaz') => '#06B6D4',
            str_contains($lowerColor, 'mint') => '#A7F3D0',
            str_contains($lowerColor, 'haki') => '#8B8B00',
            str_contains($lowerColor, 'laci') => '#1E3A8A',
            str_contains($lowerColor, 'fusya') => '#EC4899',
            str_contains($lowerColor, 'gold') => '#FFD700',
            str_contains($lowerColor, 'hardal') => '#E4A853',
            default => '#3B82F6'
        };
    }
}
