<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuickHomeSliderSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n╔══════════════════════════════════════════════════════════════╗\n";
        echo "║         🖼️  HOME SLIDER VE SETTINGS GÜNCELLEME            ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n\n";
        
        // Home sliders'ı güncelle (path'leri düzelt)
        $sliders = DB::table('home_sliders')->get();
        
        foreach ($sliders as $slider) {
            if ($slider->image && !str_starts_with($slider->image, 'sliders/')) {
                continue; // Zaten doğru format
            }
            
            // sliders/xxx.jpg formatındaysa, bırak öyle kalsın
            // API'de zaten storage_url ile tam path oluşturuluyor
            echo "✅ Slider: {$slider->title}\n";
            echo "   Görsel: {$slider->image}\n";
        }
        
        // Settings'den hero image'i kontrol et
        $heroImage = DB::table('settings')->where('key', 'hero_image')->first();
        
        if (!$heroImage) {
            // Hero image yoksa slider'dan al
            $firstSlider = DB::table('home_sliders')->first();
            if ($firstSlider && $firstSlider->image) {
                DB::table('settings')->insert([
                    'key' => 'hero_image',
                    'value' => $firstSlider->image,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "\n✅ Hero image eklendi: {$firstSlider->image}\n";
            }
        } else {
            echo "\n✅ Hero image mevcut: {$heroImage->value}\n";
        }
        
        echo "\n════════════════════════════════════════════════════════════════\n";
        echo "✅ Slider ve settings güncellendi!\n";
        echo "════════════════════════════════════════════════════════════════\n\n";
    }
}

