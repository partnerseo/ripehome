<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class DeleteSpecificProductsSeeder extends Seeder
{
    public function run(): void
    {
        $productsToDelete = [
            '3 Katlı kaktüs - açık Lila',
            '3 kat kaplumbağa turuncudan - şeftaliye kadar iptal',
            '3 kat mercan balık koyu pembe',
            '3 kat mercan balık turuncu',
            '3 kat mercan balık siyah',
            '3 kat mercan balık neon yeşil',
            '3 kat mercan balık gri yeşil',
            '3 kat mercan balık mavi turuncu',
            '3 kat mercan balık mavi yeşil',
            '3 kat mercan balık yeşil gri',
            '3 kat mercan balık yeşil mavi',
            '3 kat mercan balık',
            '3 kat palmiye yeşil',
            '3 kat neon yeşil',
            '3 kat palmiye turuncu',
            'Şeritli kimono 18 gri leopar',
            'Şeritli 19 mavi mandana',
        ];

        $this->command->info('🗑️  Belirli ürünler siliniyor...\n');

        $deletedCount = 0;

        foreach ($productsToDelete as $name) {
            $products = Product::where('name', 'LIKE', "%{$name}%")->get();
            
            foreach ($products as $product) {
                $this->command->info("  ❌ Siliniyor: {$product->name}");
                $product->delete();
                $deletedCount++;
            }
        }

        $this->command->newLine();
        $this->command->info("✅ Toplam {$deletedCount} ürün silindi!");
    }
}









