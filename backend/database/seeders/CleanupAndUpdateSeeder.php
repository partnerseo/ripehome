<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\HomeSlider;
use App\Models\FeaturedProduct;
use App\Models\FeaturedSection;

class CleanupAndUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧹 Demo veriler temizleniyor...\n');
        
        // DEMO KATEGORİLERİ SİL
        $demoCategories = [
            'havlu',
            'nevresim',
            'bornoz',
            'yatak-ortusu',
            'cocuk-koleksiyonu'
        ];
        
        $deletedCategories = 0;
        $deletedProducts = 0;
        
        foreach ($demoCategories as $slug) {
            $category = Category::where('slug', $slug)->first();
            if ($category) {
                $this->command->info("  ❌ Siliniyor: {$category->name}");
                
                // Kategoriye ait ürünleri say ve sil
                $productCount = Product::where('category_id', $category->id)->count();
                Product::where('category_id', $category->id)->delete();
                $deletedProducts += $productCount;
                
                // Kategoriyi sil
                $category->delete();
                $deletedCategories++;
            }
        }
        
        // DEMO FEATURED PRODUCTS TEMİZLE
        $featuredCount = FeaturedProduct::count();
        FeaturedProduct::truncate();
        
        $this->command->newLine();
        $this->command->info('✅ Demo veriler temizlendi!');
        if ($deletedCategories > 0) {
            $this->command->info("   📦 {$deletedCategories} kategori silindi");
            $this->command->info("   🛍️  {$deletedProducts} ürün silindi");
        }
        if ($featuredCount > 0) {
            $this->command->info("   ⭐ {$featuredCount} featured product silindi");
        }
        
        $this->command->newLine();
        $this->command->info('🖼️  Kategori görselleri güncelleniyor...\n');
        
        // KATEGORİ GÖRSELLERİNİ GÜNCELLE
        $categories = Category::all();
        $updatedCount = 0;
        $notFoundCount = 0;
        
        foreach ($categories as $category) {
            // Kategorinin ilk ürününün ilk görselini al
            $firstProduct = Product::where('category_id', $category->id)
                ->where('is_active', true)
                ->whereNotNull('images')
                ->first();
            
            if ($firstProduct && $firstProduct->images) {
                $images = is_string($firstProduct->images) 
                    ? json_decode($firstProduct->images, true) 
                    : $firstProduct->images;
                
                if (!empty($images) && isset($images[0])) {
                    $category->image = $images[0];
                    $category->save();
                    
                    $this->command->info("  ✅ {$category->name}: Görsel eklendi");
                    $updatedCount++;
                } else {
                    $this->command->warn("  ⚠️  {$category->name}: Görsel array boş");
                    $notFoundCount++;
                }
            } else {
                $this->command->warn("  ⚠️  {$category->name}: Ürün bulunamadı");
                $notFoundCount++;
            }
        }
        
        $this->command->newLine();
        $this->command->info('✅ Kategori görselleri güncellendi!');
        $this->command->info("   ✅ {$updatedCount} kategori güncellendi");
        if ($notFoundCount > 0) {
            $this->command->warn("   ⚠️  {$notFoundCount} kategoride görsel/ürün yok");
        }
        
        // GENEL GÖRSELLERİ GÜNCELLE (Slider, Featured Sections)
        $this->command->newLine();
        $this->command->info('🎨 Genel görseller güncelleniyor...\n');
        
        // Random ürün görselleri al
        $productImages = Product::where('is_active', true)
            ->whereNotNull('images')
            ->inRandomOrder()
            ->limit(20)
            ->get()
            ->map(function($product) {
                $images = is_string($product->images) 
                    ? json_decode($product->images, true) 
                    : $product->images;
                return $images[0] ?? null;
            })
            ->filter()
            ->values()
            ->toArray();
        
        if (empty($productImages)) {
            $this->command->warn('⚠️  Ürün görseli bulunamadı!');
        } else {
            $this->command->info("   📦 " . count($productImages) . " ürün görseli bulundu");
            
            $sliderCount = 0;
            $sectionCount = 0;
            
            // HOME SLIDERS GÜNCELLE
            $sliders = HomeSlider::all();
            foreach ($sliders as $index => $slider) {
                if (isset($productImages[$index])) {
                    $slider->image = $productImages[$index];
                    $slider->save();
                    $this->command->info("  ✅ Slider '{$slider->title}' güncellendi");
                    $sliderCount++;
                }
            }
            
            // FEATURED SECTIONS GÜNCELLE
            $sections = FeaturedSection::all();
            foreach ($sections as $index => $section) {
                $imgIndex = $index + count($sliders);
                if (isset($productImages[$imgIndex])) {
                    $section->image = $productImages[$imgIndex];
                    $section->save();
                    $this->command->info("  ✅ Featured Section '{$section->title}' güncellendi");
                    $sectionCount++;
                }
            }
            
            $this->command->newLine();
            $this->command->info('✅ Genel görseller güncellendi!');
            $this->command->info("   🎠 {$sliderCount} slider güncellendi");
            $this->command->info("   ⭐ {$sectionCount} featured section güncellendi");
        }
        
        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✨ Tüm işlemler tamamlandı!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();
        $this->command->info('🌐 Kontrol edin:');
        $this->command->info('   http://localhost:8080/admin/categories');
        $this->command->info('   http://localhost:5173/');
    }
}
