<?php

// Ana sayfa içeriği ekle (slider ve settings)
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║         🏠 ANA SAYFA İÇERİĞİ EKLEME                        ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Home Sliders Ekle
echo "📸 HOME SLIDERS:\n";
echo "════════════════════════════════════════════════════════════════\n";

$sliders = [
    [
        'title' => 'Doğallık, Kalite ve Şıklık',
        'subtitle' => 'Ev Tekstilinde Buluşuyor',
        'image' => 'sliders/01K7M98Y086SJ9NDZ9GQH6DV07.jpg',
        'button_text' => 'Ürünleri İncele',
        'button_link' => '/kategori/natural-kimono',
        'is_active' => 1,
        'order' => 1,
    ],
    [
        'title' => 'Premium Ev Tekstili',
        'subtitle' => 'Yaşam Kalitenizi Yükseltin',
        'image' => 'sliders/01K7Q3M9S1BMW92XPNW15YH1VN.jpg',
        'button_text' => 'Keşfet',
        'button_link' => '/kategori/jakarli-kimono',
        'is_active' => 1,
        'order' => 2,
    ],
];

foreach ($sliders as $slider) {
    DB::table('home_sliders')->insert([
        'title' => $slider['title'],
        'subtitle' => $slider['subtitle'],
        'image' => $slider['image'],
        'button_text' => $slider['button_text'],
        'button_link' => $slider['button_link'],
        'is_active' => $slider['is_active'],
        'order' => $slider['order'],
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✅ {$slider['title']}\n";
    echo "   📁 {$slider['image']}\n";
}

echo "\n";
echo "⚙️  SETTINGS:\n";
echo "════════════════════════════════════════════════════════════════\n";

// Settings Ekle
$settings = [
    ['key' => 'site_name', 'value' => 'Ripe Home'],
    ['key' => 'site_description', 'value' => 'Premium Ev Tekstili Ürünleri'],
    ['key' => 'hero_image', 'value' => 'settings/01K7Q3QDQRDQQDH8CX0P31ZRVM.jpg'],
    ['key' => 'hero_title', 'value' => 'Doğallık, Kalite ve Şıklık'],
    ['key' => 'hero_subtitle', 'value' => 'Ev Tekstilinde Buluşuyor'],
    ['key' => 'email', 'value' => 'info@ripehome.com.tr'],
    ['key' => 'phone', 'value' => '+90 534 573 06 69'],
    ['key' => 'address', 'value' => 'Sevindik Mahallesi, 2291 Sokak, No: 7, Merkezefendi, Denizli'],
    ['key' => 'instagram', 'value' => 'https://www.instagram.com/ripe_home/'],
    ['key' => 'whatsapp', 'value' => '+905345730669'],
];

foreach ($settings as $setting) {
    DB::table('settings')->insert([
        'key' => $setting['key'],
        'value' => $setting['value'],
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✅ {$setting['key']}: {$setting['value']}\n";
}

echo "\n════════════════════════════════════════════════════════════════\n";
echo "✅ Ana sayfa içeriği eklendi!\n";
echo "════════════════════════════════════════════════════════════════\n\n";

