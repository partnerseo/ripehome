<?php

declare(strict_types=1);

require __DIR__ . '/GestationalAge.php';

use Hamilelik\Engine\GestationalAge;

/**
 * Ortak vektör dosyasına karşı PHP motorunu doğrular.
 *
 *   php run-tests.php          → okunabilir rapor
 *   php run-tests.php --json   → TypeScript çıktısıyla karşılaştırmak için ham sonuç
 */
$vectorFile = __DIR__ . '/../../ga-test-vectors.json';
$data = json_decode((string) file_get_contents($vectorFile), true, flags: JSON_THROW_ON_ERROR);
$jsonMode = in_array('--json', $argv, true);

$results = [];
$failures = [];

foreach ($data['vectors'] as $vector) {
    $actual = GestationalAge::calculate(
        $vector['method'],
        $vector['input_date'],
        $vector['today'],
        $vector['cycle_length'],
    );

    $results[$vector['name']] = $actual;

    foreach ($vector['expected'] as $key => $expected) {
        if ($actual[$key] !== $expected) {
            $failures[] = sprintf(
                "%s\n    %s: beklenen %s, gelen %s",
                $vector['name'],
                $key,
                var_export($expected, true),
                var_export($actual[$key], true),
            );
        }
    }
}

// Vektör dosyasının kapsamadığı davranışlar.
$behaviour = [];

$behaviour['USG duzeltmesi GA yi kaydirir'] = GestationalAge::calculate(
    'lmp', '2026-01-05', '2026-06-22', 28,
    [['measured_on' => '2026-03-01', 'ga_days_at_measure' => 70]],
);

$behaviour['En son duzeltme gecerli'] = GestationalAge::calculate(
    'lmp', '2026-01-05', '2026-06-22', 28,
    [
        ['measured_on' => '2026-03-01', 'ga_days_at_measure' => 70],
        ['measured_on' => '2026-04-10', 'ga_days_at_measure' => 100],
    ],
);

$behaviour['Termin asiminda ilerleme kilitli'] = GestationalAge::calculate('lmp', '2026-01-05', '2026-11-01');
$behaviour['44 hafta sonrasi tamamlandi mi sorusu'] = GestationalAge::calculate('lmp', '2025-01-05', '2025-11-20');

$throws = static function (callable $fn, string $label) use (&$failures, &$behaviour): void {
    try {
        $fn();
        $failures[] = "{$label}\n    hata bekleniyordu, gelmedi";
    } catch (InvalidArgumentException $e) {
        $behaviour[$label] = ['error' => true];
    }
};

$throws(static fn () => GestationalAge::calculate('lmp', '2026-06-23', '2026-06-22'), 'Gelecek tarihli SAT reddedilir');
$throws(static fn () => GestationalAge::calculate('lmp', '2026-02-30', '2026-06-22'), 'Takvimde olmayan tarih reddedilir');
$throws(static fn () => GestationalAge::calculate('lmp', '2026-01-05', '2026-06-22', 60), 'Gecersiz dongu uzunlugu reddedilir');
$throws(static fn () => GestationalAge::calculate('bilinmeyen', '2026-01-05', '2026-06-22'), 'Bilinmeyen yontem reddedilir');

if ($jsonMode) {
    echo json_encode(['vectors' => $results, 'behaviour' => $behaviour], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
    exit($failures === [] ? 0 : 1);
}

$total = count($data['vectors']) + count($behaviour);
echo "PHP motoru — {$total} kontrol\n\n";

foreach ($data['vectors'] as $vector) {
    $r = $results[$vector['name']];
    printf("  %-52s %-8s TDT %s  T%d\n", $vector['name'], $r['display'], $r['due_date'], $r['trimester']);
}

echo "\n";
foreach ($behaviour as $label => $r) {
    printf("  %-52s %s\n", $label, isset($r['error']) ? 'hata firlatildi (beklenen)' : $r['display'] . ' · ilerleme ' . $r['progress']);
}

if ($failures !== []) {
    echo "\n" . count($failures) . " BASARISIZ:\n\n" . implode("\n\n", $failures) . "\n";
    exit(1);
}

echo "\nTumu gecti.\n";
