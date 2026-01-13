<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomeSlider;
use App\Models\FeaturedSection;
use App\Models\Product;

class UpdateGeneralImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🖼️  Genel görseller güncelleniyor...');
        
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
            $this->command->error('❌ Ürün görseli bulunamadı!');
            return;
        }
        
        $this->command->info("   📦 " . count($productImages) . " ürün görseli bulundu");
        
        // HOME SLIDERS GÜNCELLE
        $sliderCount = 0;
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
        $sectionCount = 0;
        $sections = FeaturedSection::all();
        foreach ($sections as $index => $section) {
            $imageIndex = $index + count($sliders);
            if (isset($productImages[$imageIndex])) {
                $section->image = $productImages[$imageIndex];
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
}
