<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'phone' => '+90 555 123 4567',
            'email' => 'info@luxuryhome.com',
            'address' => 'Ataşehir, İstanbul, Türkiye',
            'facebook' => 'https://facebook.com/luxuryhome',
            'instagram' => 'https://instagram.com/luxuryhome',
            'twitter' => 'https://twitter.com/luxuryhome',
            'linkedin' => 'https://linkedin.com/company/luxuryhome',
            'footer_text' => '© 2025 Luxury Home Textiles. Tüm hakları saklıdır. Premium ev tekstili ürünleri.',
        ]);
    }
}













