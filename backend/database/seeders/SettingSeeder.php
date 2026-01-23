<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'phone' => '+90 534 573 06 69',
            'email' => 'info@ripehome.com.tr',
            'address' => 'Sevindik Mahallesi, 2291 Sokak, No: 7, Merkezefendi, Denizli',
            'facebook' => null,
            'instagram' => 'https://www.instagram.com/ripe_home/',
            'twitter' => null,
            'linkedin' => null,
            'footer_text' => '© 2025 Ripe Home. Tüm hakları saklıdır. Premium ev tekstili ürünleri.',
        ]);
    }
}













