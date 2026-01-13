<?php

namespace Database\Seeders;

use App\Models\HomeSlider;
use Illuminate\Database\Seeder;

class HomeSliderSeeder extends Seeder
{
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Yeni Sezon Koleksiyonu',
                'subtitle' => 'İndirimdeki Ürünleri Keşfedin',
                'button_text' => 'Alışverişe Başla',
                'button_link' => '/products',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => '%100 Organik Pamuk Ürünler',
                'subtitle' => 'Doğal ve Sağlıklı Yaşam İçin',
                'button_text' => 'Keşfet',
                'button_link' => '/products?tag=organik',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Ücretsiz Kargo Fırsatı',
                'subtitle' => '500 TL ve Üzeri Alışverişlerde',
                'button_text' => 'Hemen İncele',
                'button_link' => '/products',
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($sliders as $slider) {
            HomeSlider::create($slider);
        }
    }
}













