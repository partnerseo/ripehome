<?php

namespace Database\Seeders;

use App\Models\FeaturedProduct;
use Illuminate\Database\Seeder;

class FeaturedProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_label' => 'PREMIUM SPA DENEYİMİ',
                'title' => 'Özel Dokuma Waffle Bornoz',
                'description' => 'İnce dokuması ve yüksek emicilik özelliği ile öne çıkan waffle dokulu bornozumuz, spa deneyimini evinize taşıyor. Premium pamuktan özel olarak dokunmuş, uzun ömürlü ve son derece konforlu.',
                'image' => 'products/waffle-bornoz.jpg',
                'tags' => ['%100 Pamuk', 'Yüksek Emicilik', 'Antibakteriyel'],
                'button_text' => 'Detayları Gör',
                'button_link' => '/products/waffle-bornoz',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'category_label' => 'SÜRDÜRÜLEBİLİR LÜKS',
                'title' => 'Doğal ve Hipoalerjenik Kenevir Bornoz',
                'description' => 'Doğal kenevir liflerinden üretilen bu özel bornoz, hem çevre dostu hem de hassas ciltler için idealdir. UV koruma özelliği ile sağlığınızı düşünen, sürdürülebilir lüksün en güzel örneği.',
                'image' => 'products/kenevir-bornoz.jpg',
                'tags' => ['Hipoalerjenik', 'Çevre Dostu', 'UV Koruma'],
                'button_text' => 'Detayları Gör',
                'button_link' => '/products/kenevir-bornoz',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'category_label' => 'ZAMANSIZ KONFOR',
                'title' => 'Premium Havlu Koleksiyonu',
                'description' => 'Uzun ömürlü, yumuşak ve emici dokuları ile her gün lüks bir deneyim sunan havlu koleksiyonumuz. Çabuk kuruyan özel dokusu ile hijyen ve konforu bir arada sunuyor.',
                'image' => 'products/premium-havlu.jpg',
                'tags' => ['Uzun Ömürlü', 'Yumuşak Doku', 'Çabuk Kurur'],
                'button_text' => 'Detayları Gör',
                'button_link' => '/category/havlu',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            FeaturedProduct::create($product);
        }
    }
}












