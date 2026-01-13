<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class UpdateCategoryImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🖼️  Kategori görselleri güncelleniyor...');
        
        $categories = Category::all();
        $updatedCount = 0;
        $noProductCount = 0;
        $noImageCount = 0;
        
        foreach ($categories as $category) {
            // Kategorinin ilk ürününün ilk görselini al
            $firstProduct = Product::where('category_id', $category->id)
                ->where('is_active', true)
                ->whereNotNull('images')
                ->first();
            
            if ($firstProduct && $firstProduct->images) {
                $images = is_string($firstProduct->images) 
                    ? json_decode($firstProduct->images, true) 
                    : $firstProduct->images;
                
                if (!empty($images) && isset($images[0])) {
                    $category->image = $images[0];
                    $category->save();
                    
                    $this->command->info("  ✅ {$category->name}: Görsel eklendi");
                    $updatedCount++;
                } else {
                    $this->command->warn("  ⚠️  {$category->name}: Ürün görseli boş");
                    $noImageCount++;
                }
            } else {
                $this->command->warn("  ⚠️  {$category->name}: Ürün bulunamadı");
                $noProductCount++;
            }
        }
        
        $this->command->newLine();
        $this->command->info('✅ Kategori görselleri güncellendi!');
        $this->command->info("   ✅ {$updatedCount} kategori güncellendi");
        if ($noProductCount > 0) {
            $this->command->warn("   ⚠️  {$noProductCount} kategoride ürün yok");
        }
        if ($noImageCount > 0) {
            $this->command->warn("   ⚠️  {$noImageCount} kategoride görsel yok");
        }
    }
}
