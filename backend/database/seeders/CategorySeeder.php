<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Havlu',
                'slug' => 'havlu',
                'description' => 'Yumuşak ve emici havlu çeşitlerimiz. Banyo havluları, el havluları ve plaj havluları.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Nevresim',
                'slug' => 'nevresim',
                'description' => 'Konforlu uyku için kaliteli nevresim takımları. Tek kişilik, çift kişilik ve king size seçenekler.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Bornoz',
                'slug' => 'bornoz',
                'description' => 'Lüks ve rahat bornoz modelleri. Banyo ve spa kullanımı için ideal.',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Yatak Örtüsü',
                'slug' => 'yatak-ortusu',
                'description' => 'Şık ve dekoratif yatak örtüleri. Yatak odanızı güzelleştirin.',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Çocuk Koleksiyonu',
                'slug' => 'cocuk-koleksiyonu',
                'description' => 'Çocuklarınız için özel tasarlanmış, yumuşak ve güvenli ev tekstili ürünleri.',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}













