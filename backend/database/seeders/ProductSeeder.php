<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tags = Tag::all();
        
        // HAVLU KATEGORİSİ
        $havluCategory = Category::where('slug', 'havlu')->first();
        
        if ($havluCategory) {
            Product::create([
                'name' => 'Premium Bambu Havlu Seti',
                'slug' => 'premium-bambu-havlu-seti',
                'description' => '<p>Bambu liflerinden üretilen premium havlu setimiz, doğal antibakteriyel özelliği ve yüksek emiciliği ile öne çıkar. Uzun ömürlü ve çevre dostu.</p><p>Set içeriği:</p><ul><li>2 adet banyo havlusu (70x140 cm)</li><li>2 adet el havlusu (50x90 cm)</li><li>2 adet yüz havlusu (30x30 cm)</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1582735689369-4fe89db7114c?w=600&h=400&fit=crop',
                    'https://images.unsplash.com/photo-1620799139834-6b8f844fbe61?w=600&h=400&fit=crop',
                ]),
                'category_id' => $havluCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-star', 'title' => '%100 Bambu', 'description' => 'Doğal bambu liflerinden üretilmiştir'],
                    ['icon' => 'heroicon-o-sparkles', 'title' => 'Antibakteriyel', 'description' => 'Doğal antibakteriyel özellik'],
                    ['icon' => 'heroicon-o-heart', 'title' => 'Süper Emici', 'description' => 'Yüksek su emme kapasitesi'],
                ]),
                'is_active' => true,
                'is_featured' => true,
                'order' => 1,
            ])->tags()->attach($tags->random(2)->pluck('id'));

            Product::create([
                'name' => 'Organik Pamuk Havlu Takımı',
                'slug' => 'organik-pamuk-havlu-takimi',
                'description' => '<p>%100 organik pamuktan üretilen havlu takımımız, hassas ciltler için idealdir. GOTS sertifikalı, kimyasal içermez.</p><p>Özellikler:</p><ul><li>Organik sertifikalı pamuk</li><li>Kimyasal içermez</li><li>Hipoalerjenik yapı</li><li>Yüksek emicilik</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1616627547584-bf28cfeefa0e?w=600&h=400&fit=crop',
                    'https://images.unsplash.com/photo-1631889993959-41b4e9c6e3c5?w=600&h=400&fit=crop',
                ]),
                'category_id' => $havluCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-check-circle', 'title' => 'GOTS Sertifikalı', 'description' => 'Organik üretim sertifikası'],
                    ['icon' => 'heroicon-o-leaf', 'title' => '%100 Organik', 'description' => 'Kimyasal içermez'],
                    ['icon' => 'heroicon-o-heart', 'title' => 'Hassas Ciltler İçin', 'description' => 'Hipoalerjenik yapı'],
                ]),
                'is_active' => true,
                'is_featured' => false,
                'order' => 2,
            ])->tags()->attach($tags->random(2)->pluck('id'));

            Product::create([
                'name' => 'Lüks Plaj Havlusu',
                'slug' => 'luks-plaj-havlusu',
                'description' => '<p>Ekstra büyük boyutu ve hızlı kuruma özelliği ile plaj ve havuz için ideal. Canlı renkleri solmaz.</p><p>Özellikler:</p><ul><li>100x180 cm büyük boy</li><li>Hızlı kuruma</li><li>UV koruma</li><li>Hafif taşıma</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1576567734310-4fdbe97922f7?w=600&h=400&fit=crop',
                ]),
                'category_id' => $havluCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-arrow-trending-up', 'title' => 'Ekstra Büyük', 'description' => '100x180 cm boyut'],
                    ['icon' => 'heroicon-o-sun', 'title' => 'Hızlı Kurur', 'description' => 'Plaj ve havuz için ideal'],
                    ['icon' => 'heroicon-o-swatch', 'title' => 'Canlı Renkler', 'description' => 'Solmayan renk kalitesi'],
                ]),
                'is_active' => true,
                'is_featured' => false,
                'order' => 3,
            ])->tags()->attach($tags->random(2)->pluck('id'));
        }

        // BORNOZ KATEGORİSİ
        $bornozCategory = Category::where('slug', 'bornoz')->first();
        
        if ($bornozCategory) {
            Product::create([
                'name' => 'Waffle Dokulu Spa Bornoz',
                'slug' => 'waffle-dokulu-spa-bornoz',
                'description' => '<p>Waffle dokulu spa bornozumuz, ince yapısı ve yüksek emiciliği ile spa konforunu evinize taşır. Premium kalite pamuktan üretilmiştir.</p><p>Özellikler:</p><ul><li>Unisex kullanım</li><li>S/M, L/XL bedenler</li><li>Kemerli tasarım</li><li>Cep detayları</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1564584217132-2271feaeb3c5?w=600&h=400&fit=crop',
                ]),
                'category_id' => $bornozCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-star', 'title' => 'Waffle Doku', 'description' => 'Özel dokuma tekniği ile üretilmiştir'],
                    ['icon' => 'heroicon-o-fire', 'title' => 'Hafif ve Nefes Alır', 'description' => 'Her mevsim kullanıma uygun'],
                    ['icon' => 'heroicon-o-sparkles', 'title' => 'Şık Tasarım', 'description' => 'Modern ve zarif görünüm'],
                ]),
                'is_active' => true,
                'is_featured' => true,
                'order' => 1,
            ])->tags()->attach($tags->random(2)->pluck('id'));

            Product::create([
                'name' => 'Kenevir Lifli Doğal Bornoz',
                'slug' => 'kenevir-lifli-dogal-bornoz',
                'description' => '<p>Doğal kenevir liflerinden üretilen bornozumuz, çevre dostu ve sürdürülebilir. Doğal UV koruma özelliği ile sağlıklı bir seçim.</p><p>Özellikler:</p><ul><li>Sürdürülebilir üretim</li><li>UV koruma</li><li>Antibakteriyel</li><li>Dayanıklı</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1605200378519-e9655ffcf4ab?w=600&h=400&fit=crop',
                ]),
                'category_id' => $bornozCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-leaf', 'title' => 'Doğal Kenevir', 'description' => 'Sürdürülebilir üretim'],
                    ['icon' => 'heroicon-o-shield-check', 'title' => 'UV Koruma', 'description' => 'Doğal UV koruma özelliği'],
                    ['icon' => 'heroicon-o-check-badge', 'title' => 'Hipoalerjenik', 'description' => 'Hassas ciltler için uygun'],
                ]),
                'is_active' => true,
                'is_featured' => true,
                'order' => 2,
            ])->tags()->attach($tags->random(2)->pluck('id'));
        }

        // NEVRESİM KATEGORİSİ
        $nevresimCategory = Category::where('slug', 'nevresim')->first();
        
        if ($nevresimCategory) {
            Product::create([
                'name' => 'Premium Saten Nevresim Takımı',
                'slug' => 'premium-saten-nevresim-takimi',
                'description' => '<p>Saten dokulu premium nevresim takımımız, ipeksi dokusu ve parlak görünümü ile yatak odanıza lüks bir hava katar. Yüksek iplik sayısı ile dayanıklıdır.</p><p>Çift kişilik set içeriği:</p><ul><li>1 adet nevresim (200x220 cm)</li><li>2 adet yastık kılıfı (50x70 cm)</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=600&h=400&fit=crop',
                ]),
                'category_id' => $nevresimCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-sparkles', 'title' => 'Saten Doku', 'description' => 'İpeksi yumuşaklık'],
                    ['icon' => 'heroicon-o-star', 'title' => 'Yüksek İplik Sayısı', 'description' => '600 thread count'],
                    ['icon' => 'heroicon-o-shield-check', 'title' => 'Dayanıklı', 'description' => 'Uzun ömürlü kullanım'],
                ]),
                'is_active' => true,
                'is_featured' => false,
                'order' => 1,
            ])->tags()->attach($tags->random(2)->pluck('id'));

            Product::create([
                'name' => 'Organik Pamuk Nevresim Seti',
                'slug' => 'organik-pamuk-nevresim-seti',
                'description' => '<p>%100 organik pamuktan üretilen nevresim setimiz, doğal ve sağlıklı bir uyku için ideal. Nefes alır yapısı ile her mevsim konfor sunar.</p><p>Özellikler:</p><ul><li>GOTS sertifikalı organik pamuk</li><li>Kimyasal içermez</li><li>Nefes alır yapı</li><li>Her mevsim kullanım</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1540518614846-7eded433c457?w=600&h=400&fit=crop',
                ]),
                'category_id' => $nevresimCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-leaf', 'title' => '%100 Organik', 'description' => 'Sertifikalı organik pamuk'],
                    ['icon' => 'heroicon-o-check-circle', 'title' => 'Nefes Alır', 'description' => 'Doğal hava sirkülasyonu'],
                    ['icon' => 'heroicon-o-heart', 'title' => 'Hassas Ciltler', 'description' => 'Kimyasal içermez'],
                ]),
                'is_active' => true,
                'is_featured' => true,
                'order' => 2,
            ])->tags()->attach($tags->random(2)->pluck('id'));

            Product::create([
                'name' => 'Bambu Nevresim Takımı',
                'slug' => 'bambu-nevresim-takimi',
                'description' => '<p>Bambu liflerinden üretilen nevresim takımımız, nefes alabilen yapısı ve yumuşacık dokusu ile her mevsim kullanım için idealdir.</p><p>Çift kişilik set içeriği:</p><ul><li>1 adet nevresim (200x220 cm)</li><li>2 adet yastık kılıfı (50x70 cm)</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1560185007-c8ad0939b69d?w=600&h=400&fit=crop',
                ]),
                'category_id' => $nevresimCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-leaf', 'title' => 'Bambu Lifi', 'description' => 'Doğal ve sürdürülebilir'],
                    ['icon' => 'heroicon-o-wind', 'title' => 'Nefes Alır', 'description' => 'Her mevsim konforlu'],
                    ['icon' => 'heroicon-o-shield-check', 'title' => 'Antibakteriyel', 'description' => 'Doğal koruma'],
                ]),
                'is_active' => true,
                'is_featured' => true,
                'order' => 3,
            ])->tags()->attach($tags->random(2)->pluck('id'));
        }

        // YATAK ÖRTÜSÜ KATEGORİSİ
        $yatakCategory = Category::where('slug', 'yatak-ortusu')->first();
        
        if ($yatakCategory) {
            Product::create([
                'name' => 'Kapitone Yatak Örtüsü',
                'slug' => 'kapitone-yatak-ortusu',
                'description' => '<p>Kapitone dokulu yatak örtümüz, şık tasarımı ve yumuşak dokusu ile yatak odanıza zarif bir dokunuş katar. Çift kişilik ebatlarda.</p><p>Özellikler:</p><ul><li>Çift kişilik: 240x260 cm</li><li>Ters yüz kullanılabilir</li><li>Makinede yıkanabilir</li><li>Çarşaf uyumlu tasarım</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&h=400&fit=crop',
                ]),
                'category_id' => $yatakCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-sparkles', 'title' => 'Kapitone Doku', 'description' => 'Şık ve zarif görünüm'],
                    ['icon' => 'heroicon-o-home', 'title' => 'Çift Kişilik', 'description' => '240x260 cm'],
                    ['icon' => 'heroicon-o-check-circle', 'title' => 'Kolay Bakım', 'description' => 'Makinede yıkanabilir'],
                ]),
                'is_active' => true,
                'is_featured' => false,
                'order' => 1,
            ])->tags()->attach($tags->random(2)->pluck('id'));

            Product::create([
                'name' => 'Nakışlı Yatak Örtüsü',
                'slug' => 'nakisli-yatak-ortusu',
                'description' => '<p>El işçiliği nakış detayları ve kaliteli kumaş ile üretilmiş yatak örtümüz, dekorasyonunuzu tamamlar.</p><p>Özellikler:</p><ul><li>El nakışlı detaylar</li><li>Çift kişilik boy</li><li>Çift taraflı kullanım</li><li>Özel tasarım</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1615800098779-1be32e60cca3?w=600&h=400&fit=crop',
                ]),
                'category_id' => $yatakCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-sparkles', 'title' => 'El Nakışı', 'description' => 'Özel tasarım detaylar'],
                    ['icon' => 'heroicon-o-paint-brush', 'title' => 'Şık Tasarım', 'description' => 'Modern ve zarif'],
                    ['icon' => 'heroicon-o-arrow-path', 'title' => 'Çift Taraflı', 'description' => 'İki farklı kullanım'],
                ]),
                'is_active' => true,
                'is_featured' => false,
                'order' => 2,
            ])->tags()->attach($tags->random(2)->pluck('id'));
        }

        // ÇOCUK KOLEKSİYONU
        $cocukCategory = Category::where('slug', 'cocuk-koleksiyonu')->first();
        
        if ($cocukCategory) {
            Product::create([
                'name' => 'Çocuk Nevresim Takımı - Yıldızlar',
                'slug' => 'cocuk-nevresim-takimi-yildizlar',
                'description' => '<p>Renkli yıldız desenli çocuk nevresim takımımız, %100 pamuktan üretilmiştir. Hassas çocuk ciltleri için güvenlidir.</p><p>Tek kişilik set içeriği:</p><ul><li>1 adet nevresim (160x220 cm)</li><li>1 adet yastık kılıfı (50x70 cm)</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&h=400&fit=crop',
                ]),
                'category_id' => $cocukCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-star', 'title' => 'Renkli Desenler', 'description' => 'Çocukların sevdiği tasarımlar'],
                    ['icon' => 'heroicon-o-shield-check', 'title' => 'Güvenli', 'description' => 'Zararlı kimyasal içermez'],
                    ['icon' => 'heroicon-o-heart', 'title' => '%100 Pamuk', 'description' => 'Nefes alır doğal kumaş'],
                ]),
                'is_active' => true,
                'is_featured' => false,
                'order' => 1,
            ])->tags()->attach($tags->random(2)->pluck('id'));

            Product::create([
                'name' => 'Renkli Çocuk Nevresim Seti',
                'slug' => 'renkli-cocuk-nevresim-seti',
                'description' => '<p>Eğlenceli desenler ve yumuşak kumaş ile çocuğunuzun konforlu uyumasını sağlar. Hipoalerjenik yapısı hassas ciltler için güvenlidir.</p><p>Özellikler:</p><ul><li>Eğlenceli çocuk desenleri</li><li>Hipoalerjenik yapı</li><li>Kolay bakım</li><li>Yumuşak doku</li></ul>',
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=600&h=400&fit=crop',
                ]),
                'category_id' => $cocukCategory->id,
                'features' => json_encode([
                    ['icon' => 'heroicon-o-face-smile', 'title' => 'Eğlenceli Desenler', 'description' => 'Çocukların sevdiği renkler'],
                    ['icon' => 'heroicon-o-shield-exclamation', 'title' => 'Hipoalerjenik', 'description' => 'Hassas ciltler için güvenli'],
                    ['icon' => 'heroicon-o-star', 'title' => 'Yumuşak', 'description' => 'Rahat uyku için ideal'],
                ]),
                'is_active' => true,
                'is_featured' => true,
                'order' => 2,
            ])->tags()->attach($tags->random(2)->pluck('id'));
        }
    }
}


