<?php
function lmpFrom(string $method, string $date, int $cycle): string {
    $d = new DateTimeImmutable($date, new DateTimeZone('UTC'));
    return match ($method) {
        'lmp'        => $d->modify(sprintf('%+d days', $cycle - 28))->format('Y-m-d'),
        'due_date'   => $d->modify('-280 days')->format('Y-m-d'),
        'conception' => $d->modify('-14 days')->format('Y-m-d'),
        'ivf_d5'     => $d->modify('-19 days')->format('Y-m-d'),
        'ivf_d3'     => $d->modify('-17 days')->format('Y-m-d'),
    };
}
// Not: lmp yönteminde döngü düzeltmesi TDT'ye eklenir; eşdeğer olarak
// "etkin SAT" ileri kaydırılır (uzun döngü = geç ovülasyon = daha genç gebelik).
function build(array $c): array {
    $tz = new DateTimeZone('UTC');
    $lmp = lmpFrom($c['method'], $c['input_date'], $c['cycle_length'] ?? 28);
    $lmpD = new DateTimeImmutable($lmp, $tz);
    $due = $lmpD->modify('+280 days');
    $today = new DateTimeImmutable($c['today'], $tz);
    $ga = (int) $lmpD->diff($today)->days * ($today < $lmpD ? -1 : 1);
    $week = intdiv($ga, 7);
    $day = $ga % 7;
    $trimester = $ga < 98 ? 1 : ($ga < 196 ? 2 : 3);
    return [
        'name'          => $c['name'],
        'method'        => $c['method'],
        'input_date'    => $c['input_date'],
        'cycle_length'  => $c['cycle_length'] ?? 28,
        'today'         => $c['today'],
        'expected'      => [
            'lmp_date'    => $lmp,
            'due_date'    => $due->format('Y-m-d'),
            'ga_days'     => $ga,
            'week'        => $week,
            'day'         => $day,
            'display'     => "{$week}h {$day}g",
            'trimester'   => $trimester,
            'days_left'   => (int) $today->diff($due)->days * ($due < $today ? -1 : 1),
        ],
    ];
}
$cases = [
    ['name'=>'SAT, standart 28 gunluk dongu','method'=>'lmp','input_date'=>'2026-01-05','today'=>'2026-06-22'],
    ['name'=>'SAT, uzun dongu (35 gun) - TDT 7 gun ileri','method'=>'lmp','input_date'=>'2026-01-05','cycle_length'=>35,'today'=>'2026-06-22'],
    ['name'=>'SAT, kisa dongu (21 gun) - TDT 7 gun geri','method'=>'lmp','input_date'=>'2026-01-05','cycle_length'=>21,'today'=>'2026-06-22'],
    ['name'=>'Doktorun verdigi TDT girildi','method'=>'due_date','input_date'=>'2026-10-12','today'=>'2026-06-22'],
    ['name'=>'Gebe kalma tarihi biliniyor','method'=>'conception','input_date'=>'2026-01-19','today'=>'2026-06-22'],
    ['name'=>'IVF 5. gun blastosist transferi','method'=>'ivf_d5','input_date'=>'2026-01-24','today'=>'2026-06-22'],
    ['name'=>'IVF 3. gun embriyo transferi','method'=>'ivf_d3','input_date'=>'2026-01-22','today'=>'2026-06-22'],
    ['name'=>'Ilk gun - GA sifir','method'=>'lmp','input_date'=>'2026-06-22','today'=>'2026-06-22'],
    ['name'=>'Trimester siniri: 13h6g (son 1. trimester gunu)','method'=>'lmp','input_date'=>'2026-01-05','today'=>'2026-04-12'],
    ['name'=>'Trimester siniri: 14h0g (2. trimester basi)','method'=>'lmp','input_date'=>'2026-01-05','today'=>'2026-04-13'],
    ['name'=>'Trimester siniri: 27h6g (son 2. trimester gunu)','method'=>'lmp','input_date'=>'2026-01-05','today'=>'2026-07-19'],
    ['name'=>'Trimester siniri: 28h0g (3. trimester basi)','method'=>'lmp','input_date'=>'2026-01-05','today'=>'2026-07-20'],
    ['name'=>'Tam terminde: 40h0g','method'=>'lmp','input_date'=>'2026-01-05','today'=>'2026-10-12'],
    ['name'=>'Termin asimi: 41h3g - ilerleme %100de kilitlenir','method'=>'lmp','input_date'=>'2026-01-05','today'=>'2026-10-22'],
    ['name'=>'Artik yil: 29 Subat SAT','method'=>'lmp','input_date'=>'2024-02-29','today'=>'2024-08-15'],
    ['name'=>'Artik yili asan gebelik','method'=>'lmp','input_date'=>'2023-08-01','today'=>'2024-03-01'],
    ['name'=>'Yaz saati gecisi araligi (Mart)','method'=>'lmp','input_date'=>'2026-02-20','today'=>'2026-04-05'],
];
$out = ['_note'=>'Gebelik yasi motoru ortak test vektorleri. PHP ve TypeScript uygulamalari bu dosyayi okuyup ayni sonucu uretmelidir. Tum tarihler kullanicinin yerel takvim gunudur (saat degil).','vectors'=>array_map('build', $cases)];
file_put_contents(getcwd().'/ga-test-vectors.json', json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n");
foreach ($out['vectors'] as $v) {
    printf("%-52s SAT %s  TDT %s  %-8s T%d\n", $v['name'], $v['expected']['lmp_date'], $v['expected']['due_date'], $v['expected']['display'], $v['expected']['trimester']);
}
