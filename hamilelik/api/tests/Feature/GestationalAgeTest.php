<?php

declare(strict_types=1);

namespace Tests\Feature;

use Hamilelik\Engine\GestationalAge;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Motoru, TypeScript ikiziyle paylaşılan vektör dosyasına karşı doğrular.
 * Bu dosyadaki bir kırılma, istemci ile sunucunun farklı hafta hesaplaması demektir.
 */
class GestationalAgeTest extends TestCase
{
    /** @return array<string, array{array<string, mixed>}> */
    public static function vectors(): array
    {
        $path = dirname(__DIR__, 3).'/ga-test-vectors.json';
        $data = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        return collect($data['vectors'])
            ->mapWithKeys(fn (array $v): array => [$v['name'] => [$v]])
            ->all();
    }

    /** @param array<string, mixed> $vector */
    #[Test]
    #[DataProvider('vectors')]
    public function it_matches_the_shared_vector(array $vector): void
    {
        $actual = GestationalAge::calculate(
            $vector['method'],
            $vector['input_date'],
            $vector['today'],
            $vector['cycle_length'],
        );

        foreach ($vector['expected'] as $key => $expected) {
            $this->assertSame($expected, $actual[$key], "Alan: {$key}");
        }
    }

    #[Test]
    public function long_cycles_push_the_due_date_later(): void
    {
        // Uzun döngü = geç ovülasyon = daha genç gebelik = ileri TDT.
        $standard = GestationalAge::calculate('lmp', '2026-01-05', '2026-06-22', 28);
        $long = GestationalAge::calculate('lmp', '2026-01-05', '2026-06-22', 35);
        $short = GestationalAge::calculate('lmp', '2026-01-05', '2026-06-22', 21);

        $this->assertSame('2026-10-12', $standard['due_date']);
        $this->assertSame('2026-10-19', $long['due_date']);
        $this->assertSame('2026-10-05', $short['due_date']);
        $this->assertLessThan($standard['ga_days'], $long['ga_days']);
        $this->assertGreaterThan($standard['ga_days'], $short['ga_days']);
    }

    #[Test]
    public function the_latest_redating_wins(): void
    {
        $result = GestationalAge::calculate('lmp', '2026-01-05', '2026-06-22', 28, [
            ['measured_on' => '2026-03-01', 'ga_days_at_measure' => 70],
            ['measured_on' => '2026-04-10', 'ga_days_at_measure' => 100],
        ]);

        // 2026-04-10 eksi 100 gün = 2025-12-31 etkin SAT.
        $this->assertSame('2025-12-31', $result['lmp_date']);
        $this->assertSame(173, $result['ga_days']);
    }

    #[Test]
    public function progress_locks_at_one_past_term(): void
    {
        $result = GestationalAge::calculate('lmp', '2026-01-05', '2026-11-01');

        $this->assertSame(1.0, $result['progress']);
        $this->assertTrue($result['is_overdue']);
        $this->assertLessThan(0, $result['days_left']);
    }

    #[Test]
    public function it_flags_pregnancies_past_forty_four_weeks(): void
    {
        $this->assertFalse(GestationalAge::calculate('lmp', '2026-01-05', '2026-11-01')['needs_completion_prompt']);
        $this->assertTrue(GestationalAge::calculate('lmp', '2025-01-05', '2025-11-20')['needs_completion_prompt']);
    }

    /** @return array<string, array{callable}> */
    public static function invalidInputs(): array
    {
        return [
            'gelecek tarihli SAT' => [fn () => GestationalAge::calculate('lmp', '2026-06-23', '2026-06-22')],
            'takvimde olmayan tarih' => [fn () => GestationalAge::calculate('lmp', '2026-02-30', '2026-06-22')],
            'hatalı biçim' => [fn () => GestationalAge::calculate('lmp', '05.01.2026', '2026-06-22')],
            'geçersiz döngü uzunluğu' => [fn () => GestationalAge::calculate('lmp', '2026-01-05', '2026-06-22', 60)],
            'bilinmeyen yöntem' => [fn () => GestationalAge::calculate('gebelik', '2026-01-05', '2026-06-22')],
        ];
    }

    #[Test]
    #[DataProvider('invalidInputs')]
    public function it_rejects_invalid_input(callable $call): void
    {
        $this->expectException(InvalidArgumentException::class);

        $call();
    }
}
