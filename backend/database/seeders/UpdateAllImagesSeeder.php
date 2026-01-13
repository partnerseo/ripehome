<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\HomeSlider;
use App\Models\FeaturedProduct;
use App\Models\FeaturedSection;
use App\Models\Setting;
use App\Models\Page;

class UpdateAllImagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🖼️  Tüm görseller güncelleniyor...\n');
        
        // Random ürün görselleri al (30 adet)
        $productImages = Product::where('is_active', true)
            ->whereNotNull('images')
            ->inRandomOrder()
            ->limit(30)
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
            $this->command->error('❌ Ürün görseli bulunamadı!');
            return;
        }
        
        $this->command->info("📦 " . count($productImages) . " ürün görseli bulundu\n");
        
        // 1. KATEGORİ GÖRSELLERİ
        $this->command->info('📂 Kategori görselleri güncelleniyor...');
        $categories = Category::whereNull('image')->orWhere('image', '')->get();
        
        foreach ($categories as $index => $category) {
            // Kategorinin ilk ürününün görseli
            $firstProduct = Product::where('category_id', $category->id)
                ->where('is_active', true)
                ->whereNotNull('images')
                ->first();
            
            if ($firstProduct && $firstProduct->images) {
                $images = is_string($firstProduct->images) 
                    ? json_decode($firstProduct->images, true) 
                    : $firstProduct->images;
                
                if (!empty($images)) {
                    $category->image = $images[0];
                    $category->save();
                    $this->command->info("  ✅ {$category->name}");
                }
            }
        }
        
        // 2. HOME SLIDERS
        $this->command->info("\n🎠 Home Slider görselleri güncelleniyor...");
        $sliders = HomeSlider::all();
        
        if ($sliders->isEmpty()) {
            // Slider yoksa oluştur
            for ($i = 0; $i < min(3, count($productImages)); $i++) {
                HomeSlider::create([
                    'title' => 'Premium Koleksiyon ' . ($i + 1),
                    'subtitle' => 'Lüks ev tekstili ürünleri',
                    'button_text' => 'Koleksiyonu Keşfet',
                    'button_link' => '/products',
                    'image' => $productImages[$i],
                    'is_active' => true,
                    'order' => $i + 1,
                ]);
                $this->command->info("  ✅ Slider " . ($i + 1) . " oluşturuldu");
            }
        } else {
            // Mevcut slider'ları güncelle
            foreach ($sliders as $index => $slider) {
                if (isset($productImages[$index])) {
                    $slider->image = $productImages[$index];
                    $slider->save();
                    $this->command->info("  ✅ Slider {$slider->id} güncellendi");
                }
            }
        }
        
        // 3. FEATURED SECTIONS (Why Choose Us, About vs.)
        $this->command->info("\n⭐ Featured Section görselleri güncelleniyor...");
        $sections = FeaturedSection::all();
        
        if ($sections->isEmpty()) {
            // Section yoksa oluştur
            $sectionData = [
                [
                    'title' => 'Premium Kalite',
                    'description' => 'Sadece en kaliteli hammaddelerden üretilmiş ürünler.',
                    'icon' => 'check-circle',
                ],
                [
                    'title' => 'Hızlı Teslimat',
                    'description' => 'Siparişleriniz aynı gün kargoya teslim edilir.',
                    'icon' => 'truck',
                ],
                [
                    'title' => 'Toptan Fiyat',
                    'description' => 'Toptan alımlarda özel indirim fırsatları.',
                    'icon' => 'tag',
                ],
            ];
            
            foreach ($sectionData as $index => $data) {
                $imgIndex = count($sliders) + $index;
                FeaturedSection::create([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'icon' => $data['icon'],
                    'image' => $productImages[$imgIndex] ?? $productImages[0],
                    'is_active' => true,
                    'order' => $index + 1,
                ]);
                $this->command->info("  ✅ Section '{$data['title']}' oluşturuldu");
            }
        } else {
            // Mevcut section'ları güncelle
            foreach ($sections as $index => $section) {
                $imgIndex = count($sliders) + $index;
                if (isset($productImages[$imgIndex])) {
                    $section->image = $productImages[$imgIndex];
                    $section->save();
                    $this->command->info("  ✅ Section {$section->id} güncellendi");
                }
            }
        }
        
        // 4. FEATURED PRODUCTS
        $this->command->info("\n🌟 Featured Products güncelleniyor...");
        FeaturedProduct::truncate();
        
        $featuredProductsToAdd = Product::where('is_active', true)
            ->whereNotNull('images')
            ->inRandomOrder()
            ->limit(6)
            ->get();
        
        foreach ($featuredProductsToAdd as $index => $product) {
            FeaturedProduct::create([
                'title' => $product->name,
                'description' => strip_tags($product->description),
                'button_text' => 'Detayları Gör',
                'button_link' => '/product/' . $product->slug,
                'image' => is_string($product->images) 
                    ? json_decode($product->images, true)[0] ?? null
                    : $product->images[0] ?? null,
                'category_label' => $product->category->name ?? '',
                'tags' => json_encode(['Premium', 'Kaliteli']),
                'is_active' => true,
                'order' => $index + 1,
            ]);
            $this->command->info("  ✅ Featured Product " . ($index + 1) . " eklendi");
        }
        
        // 5. SETTINGS (Logo, Favicon)
        $this->command->info("\n⚙️  Site ayarları güncelleniyor...");
        
        $setting = Setting::first();
        if (!$setting) {
            $setting = Setting::create([
                'logo' => $productImages[0] ?? null,
                'favicon' => $productImages[1] ?? null,
            ]);
            $this->command->info("  ✅ Site ayarları oluşturuldu");
        } else {
            $setting->logo = $productImages[0] ?? $setting->logo;
            $setting->favicon = $productImages[1] ?? $setting->favicon;
            $setting->save();
            $this->command->info("  ✅ Site ayarları güncellendi");
        }
        
        // 6. PAGES (Hakkımızda, İletişim vs.)
        $this->command->info("\n📄 Sayfa görselleri güncelleniyor...");
        
        $pages = Page::whereNull('image')->orWhere('image', '')->get();
        foreach ($pages as $index => $page) {
            $imgIndex = 15 + $index;
            if (isset($productImages[$imgIndex])) {
                $page->image = $productImages[$imgIndex];
                $page->save();
                $this->command->info("  ✅ {$page->title} sayfası güncellendi");
            }
        }
        
        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✨ Tüm görseller başarıyla güncellendi!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
