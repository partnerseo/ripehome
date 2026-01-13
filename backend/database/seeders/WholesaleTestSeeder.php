<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WholesaleTestSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $productCount = 0;
        
        foreach ($categories as $category) {
            // Her kategoriye 2 toptan ürün ekle
            for ($i = 1; $i <= 2; $i++) {
                Product::create([
                    'name' => $category->name . ' - Toptan Set ' . $i,
                    'slug' => Str::slug($category->name . '-toptan-set-' . $i),
                    'description' => '<p>Toptan satış için ideal ' . $category->name . ' seti. Kurumsal siparişler için özel fiyatlandırma.</p><p>Özellikler:</p><ul><li>Toptan fiyat avantajı</li><li>Hızlı teslimat</li><li>Özel paketleme</li><li>Kurumsal fatura</li></ul>',
                    'images' => json_encode([
                        'https://via.placeholder.com/600x400/10B981/ffffff?text=' . urlencode($category->name . ' Set ' . $i),
                    ]),
                    'category_id' => $category->id,
                    'features' => json_encode([
                        [
                            'icon' => 'check',
                            'title' => 'Toptan Fiyat',
                            'description' => 'Özel toptan fiyatlandırma ve hacim indirimleri'
                        ],
                        [
                            'icon' => 'truck',
                            'title' => 'Hızlı Kargo',
                            'description' => 'Aynı gün kargoya verilir, 1-2 gün içinde teslim'
                        ],
                        [
                            'icon' => 'package',
                            'title' => 'Uygun Paketleme',
                            'description' => 'Nakliyeye uygun özel kutulama ve paketleme'
                        ],
                        [
                            'icon' => 'document',
                            'title' => 'Kurumsal Fatura',
                            'description' => 'E-Fatura ve e-Arşiv fatura desteği'
                        ],
                    ]),
                    'is_active' => true,
                    'is_featured' => false,
                    'order' => 100 + $i,
                ]);
                
                $productCount++;
            }
        }
        
        $this->command->info('✅ Toptan sipariş için ' . $productCount . ' test ürün oluşturuldu!');
        $this->command->info('📦 Kategoriler: ' . $categories->count());
        $this->command->info('🛒 Test URL: http://localhost:5174/ripehome/toptan-siparis');
    }
}



