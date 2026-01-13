<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user oluştur
        User::firstOrCreate(
            ['email' => 'admin@luxuryhome.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
            ]
        );

        // Diğer seeder'lar
        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            ProductSeeder::class,
            WholesaleTestSeeder::class,
            PageSeeder::class,
            HomeSliderSeeder::class,
            FeaturedSectionSeeder::class,
            FeaturedProductSeeder::class,
            SettingSeeder::class,
            
            // Demo verileri temizle ve görselleri güncelle
            // CleanDemoDataSeeder::class,  // İsteğe bağlı - demo verileri silmek için aktif edin
            UpdateCategoryImagesSeeder::class,
            UpdateGeneralImagesSeeder::class,
        ]);
    }
}
