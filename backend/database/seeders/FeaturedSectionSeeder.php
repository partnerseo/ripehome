<?php

namespace Database\Seeders;

use App\Models\FeaturedSection;
use Illuminate\Database\Seeder;

class FeaturedSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'title' => 'Ücretsiz Kargo',
                'description' => '500 TL ve üzeri tüm alışverişlerde ücretsiz kargo avantajı',
                'icon' => 'heroicon-o-truck',
                'link' => '/kargo-ve-iade',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => '%100 Doğal Ürünler',
                'description' => 'Organik ve doğal malzemelerden üretilmiş, çevre dostu ürünler',
                'icon' => 'heroicon-o-leaf',
                'link' => '/products?tag=organik',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Hızlı Teslimat',
                'description' => '1-3 iş günü içinde kapınızda, güvenli paketleme ile',
                'icon' => 'heroicon-o-bolt',
                'link' => '/kargo-ve-iade',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'title' => 'Kolay İade',
                'description' => '14 gün içinde koşulsuz iade, paranız geri ödemesi garantisi',
                'icon' => 'heroicon-o-arrow-path',
                'link' => '/kargo-ve-iade',
                'is_active' => true,
                'order' => 4,
            ],
        ];

        foreach ($sections as $section) {
            FeaturedSection::create($section);
        }
    }
}













