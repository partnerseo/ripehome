<?php

// Kategorilere görseller ekle
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║         🖼️  KATEGORİ GÖRSELLERİ GÜNCELLEME                 ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$categories = DB::table('categories')->get();

foreach ($categories as $category) {
    // Bu kategoriden ilk ürünün ilk görselini al
    $product = DB::table('products')
        ->where('category_id', $category->id)
        ->whereNotNull('images')
        ->first();
    
    if ($product) {
        $imagesRaw = $product->images;
        
        // SQLite JSON field'i string olarak döndürür, dış tırnakları kaldır
        if (is_string($imagesRaw) && str_starts_with($imagesRaw, '"') && str_ends_with($imagesRaw, '"')) {
            $imagesRaw = substr($imagesRaw, 1, -1);
        }
        
        // Escaped backslash'leri düzelt
        $imagesRaw = str_replace('\\/', '/', $imagesRaw);
        $imagesRaw = str_replace('\\"', '"', $imagesRaw);
        
        // JSON decode et
        $images = json_decode($imagesRaw, true);
        
        if (is_array($images) && count($images) > 0) {
            $firstImage = $images[0];
            
            // Backslash'leri temizle
            $firstImage = str_replace('\\/', '/', $firstImage);
            
            // Kategori görselini güncelle
            DB::table('categories')
                ->where('id', $category->id)
                ->update(['image' => $firstImage]);
            
            echo "✅ {$category->name}: {$firstImage}\n";
        } else {
            echo "⚠️  {$category->name}: JSON parse hatası\n";
            echo "   Raw: " . substr($imagesRaw, 0, 100) . "\n";
        }
    } else {
        echo "⚠️  {$category->name}: Ürün bulunamadı\n";
    }
}

echo "\n════════════════════════════════════════════════════════════════\n";
echo "✅ Kategori görselleri güncellendi!\n";
echo "════════════════════════════════════════════════════════════════\n\n";

