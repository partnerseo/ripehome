<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pregnancy;

/**
 * Hastane çantası listesi.
 *
 * Tıbbi bir liste değil, pratik bir hatırlatma — bu yüzden hekim onayı
 * kapısından geçmez. Kullanıcı kendi maddesini ekleyebilir, silebilir.
 */
class ChecklistTemplate
{
    /** @var list<array{key: string, title: string, group: string}> */
    private const ITEMS = [
        ['key' => 'kimlik', 'title' => 'Kimlik ve sağlık karnesi', 'group' => 'belgeler'],
        ['key' => 'tahliller', 'title' => 'Gebelik takip dosyası ve tahliller', 'group' => 'belgeler'],
        ['key' => 'sigorta', 'title' => 'Sigorta / hastane bilgileri', 'group' => 'belgeler'],

        ['key' => 'gecelik', 'title' => 'Önü açılabilen gecelik', 'group' => 'anne'],
        ['key' => 'sabahlik', 'title' => 'Sabahlık ve terlik', 'group' => 'anne'],
        ['key' => 'ic_camasiri', 'title' => 'Yüksek bel iç çamaşırı', 'group' => 'anne'],
        ['key' => 'ped', 'title' => 'Lohusa pedi', 'group' => 'anne'],
        ['key' => 'emzirme_sutyeni', 'title' => 'Emzirme sütyeni ve göğüs pedi', 'group' => 'anne'],
        ['key' => 'kisisel_bakim', 'title' => 'Kişisel bakım malzemeleri', 'group' => 'anne'],
        ['key' => 'sarj', 'title' => 'Telefon şarj aleti (uzun kablo)', 'group' => 'anne'],
        ['key' => 'atistirmalik', 'title' => 'Su ve atıştırmalık', 'group' => 'anne'],
        ['key' => 'tasima_cikis', 'title' => 'Çıkış için rahat kıyafet', 'group' => 'anne'],

        ['key' => 'zibin', 'title' => 'Zıbın ve body (birkaç beden)', 'group' => 'bebek'],
        ['key' => 'tulum', 'title' => 'Tulum', 'group' => 'bebek'],
        ['key' => 'battaniye', 'title' => 'Battaniye veya kundak', 'group' => 'bebek'],
        ['key' => 'bez', 'title' => 'Yenidoğan bezi ve ıslak mendil', 'group' => 'bebek'],
        ['key' => 'sapka_corap', 'title' => 'Şapka, çorap, eldiven', 'group' => 'bebek'],
        ['key' => 'oto_koltugu', 'title' => 'Bebek oto koltuğu (çıkış için zorunlu)', 'group' => 'bebek'],
    ];

    /**
     * Listeyi gebeliğe kurar. Zaten kurulmuşsa tekrar eklemez.
     *
     * @return int Eklenen madde sayısı.
     */
    public function seed(Pregnancy $pregnancy): int
    {
        $existing = $pregnancy->checklistItems()->whereNotNull('template_key')->pluck('template_key')->all();
        $added = 0;

        foreach (self::ITEMS as $index => $item) {
            if (in_array($item['key'], $existing, true)) {
                continue;
            }

            $pregnancy->checklistItems()->create([
                'template_key' => $item['key'],
                'title' => $item['title'],
                'group' => $item['group'],
                'sort' => $index * 10,
            ]);
            $added++;
        }

        return $added;
    }
}
