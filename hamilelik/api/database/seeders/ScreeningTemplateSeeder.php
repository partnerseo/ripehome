<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ScreeningTemplate;
use Illuminate\Database\Seeder;

/**
 * Türkiye tetkik takvimi taslağı.
 *
 * Hepsi "draft" durumunda gelir ve hekim onayı verilene kadar API'den
 * dönmez — yayına çıkmaları için gözden geçiren kişi ve tarih girilmelidir.
 *
 * source_refs alanındaki kaynakların güncel sürüm bilgisi ve bağlantısı,
 * gözden geçirme sırasında hekimle birlikte doldurulacak; buraya doğrulanmamış
 * bağlantı yazılmadı.
 */
class ScreeningTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['label' => 'T.C. Sağlık Bakanlığı — Doğum Öncesi Bakım Yönetim Rehberi', 'url' => null],
            ['label' => 'TJOD klinik uygulama kılavuzları', 'url' => null],
            ['label' => 'WHO antenatal bakım önerileri', 'url' => null],
        ];

        $templates = [
            ['ilk_muayene', 'İlk muayene ve temel tetkikler', 'visit', 6, 10, false,
                'Gebeliğin doğrulanması, kan sayımı, kan grubu, idrar tetkiki ve ilk değerlendirme.'],
            ['nt_ikili', 'NT (ense saydamlığı) ve ikili tarama', 'usg', 11, 14, false,
                'Kromozomal risk değerlendirmesi. Zaman aralığı dar, kaçırılmamalı.'],
            ['uclu_dortlu', 'Üçlü / dörtlü tarama', 'lab', 16, 18, true,
                'İkili tarama yapılmadıysa veya ek değerlendirme isteniyorsa.'],
            ['detayli_usg', 'Detaylı ultrason (anomali taraması)', 'usg', 18, 22, false,
                'Bebeğin organ gelişiminin ayrıntılı değerlendirilmesi.'],
            ['ogtt', 'Şeker yükleme testi (OGTT)', 'lab', 24, 28, false,
                'Gebelik şekeri taraması.'],
            ['anti_d', 'Anti-D immünglobulin', 'vaccine', 28, 28, true,
                'Kan grubu Rh negatif olan anneler için.'],
            ['tdap', 'Tdap aşısı', 'vaccine', 27, 36, false,
                'Bebeği doğumdan sonraki ilk aylarda boğmacaya karşı korur.'],
            ['kontrol_2haftalik', 'İki haftada bir kontrol', 'visit', 28, 36, false,
                'Tansiyon, kilo, bebek gelişimi takibi.'],
            ['kontrol_haftalik', 'Haftalık kontrol ve NST', 'visit', 36, 40, false,
                'Doğuma hazırlık dönemi takibi.'],
        ];

        foreach ($templates as $index => [$code, $name, $category, $start, $end, $optional, $description]) {
            ScreeningTemplate::updateOrCreate(
                ['code' => $code, 'locale' => 'tr', 'country' => 'TR'],
                [
                    'name' => $name,
                    'description' => $description,
                    'category' => $category,
                    'week_start' => $start,
                    'week_end' => $end,
                    'is_optional' => $optional,
                    'sort' => $index * 10,
                    'source_refs' => $sources,
                    // Bilinçli olarak taslak: hekim onayı olmadan kullanıcıya gitmez.
                    'status' => ScreeningTemplate::STATUS_DRAFT,
                ],
            );
        }
    }
}
