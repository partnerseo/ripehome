<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\HomeSlider;
use App\Models\FeaturedProduct;

class CleanDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🗑️  Demo veriler temizleniyor...');
        
        // Demo kategorileri sil (Havlu, Nevresim, Bornoz, Yatak Örtüsü, Çocuk Koleksiyonu)
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
        
        // Demo featured products temizle
        $featuredCount = FeaturedProduct::count();
        FeaturedProduct::truncate();
        
        $this->command->newLine();
        $this->command->info('✅ Demo veriler temizlendi!');
        $this->command->info("   📦 {$deletedCategories} kategori silindi");
        $this->command->info("   🛍️  {$deletedProducts} ürün silindi");
        $this->command->info("   ⭐ {$featuredCount} featured product silindi");
    }
}
