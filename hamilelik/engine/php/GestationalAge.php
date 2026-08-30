<?php

declare(strict_types=1);

namespace Hamilelik\Engine;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Gebelik yaşı motoru.
 *
 * Uygulamanın tamamı bu hesabın üzerinde durur; bir günlük kayma yanlış haftada
 * içerik ve yanlış tarihte tetkik uyarısı demektir. Bu sınıfın TypeScript ikizi
 * (engine/ts/gestationalAge.ts) aynı test vektörleriyle doğrulanır — ikisi her
 * girdide birebir aynı sonucu vermek zorundadır.
 *
 * Tüm hesap takvim GÜNÜ üzerinden yapılır, saat üzerinden değil. Tarihler
 * "Y-m-d" biçiminde, kullanıcının yerel takvim günü olarak verilir; saat dilimi
 * ve yaz saati geçişleri sonucu etkilemez.
 */
final class GestationalAge
{
    /** Tam term: 40 hafta. */
    public const TOTAL_DAYS = 280;

    /** 1. trimesterin bitişi (14h0g bu günde başlar). */
    private const TRIMESTER_2_START = 98;

    /** 2. trimesterin bitişi (28h0g bu günde başlar). */
    private const TRIMESTER_3_START = 196;

    /** Bu eşiği aşınca kullanıcıya "gebelik tamamlandı mı?" sorulur (44 hafta). */
    private const COMPLETION_PROMPT_DAYS = 308;

    /**
     * Giriş yönteminden etkin SAT'ı (son adet tarihi) türetir.
     *
     * Uzun döngü geç ovülasyon demektir: gebelik daha gençtir, TDT ileri gider.
     * Bunu tek kaynaktan yürütmek için düzeltmeyi TDT'ye değil etkin SAT'a
     * uyguluyoruz — GA ve TDT aynı değerden türesin.
     */
    public static function effectiveLmp(string $method, string $inputDate, int $cycleLength = 28): string
    {
        $date = self::parse($inputDate);

        if ($cycleLength < 20 || $cycleLength > 45) {
            throw new InvalidArgumentException("Döngü uzunluğu 20-45 gün aralığında olmalı: {$cycleLength}");
        }

        return match ($method) {
            'lmp'        => self::addDays($date, $cycleLength - 28),
            'due_date'   => self::addDays($date, -self::TOTAL_DAYS),
            'conception' => self::addDays($date, -14),
            'ivf_d5'     => self::addDays($date, -19),
            'ivf_d3'     => self::addDays($date, -17),
            default      => throw new InvalidArgumentException("Bilinmeyen giriş yöntemi: {$method}"),
        };
    }

    /**
     * Bir gebeliğin bugünkü durumunu hesaplar.
     *
     * @param  list<array{measured_on: string, ga_days_at_measure: int}>  $redatings
     *         USG düzeltmeleri. En son ölçüm tarihli olan geçerlidir; orijinal
     *         SAT korunur, düzeltme kaldırılınca hesap eski hâline döner.
     * @return array{
     *     lmp_date: string, due_date: string, ga_days: int, week: int, day: int,
     *     display: string, trimester: int, days_left: int, progress: float,
     *     is_overdue: bool, needs_completion_prompt: bool
     * }
     */
    public static function calculate(
        string $method,
        string $inputDate,
        string $today,
        int $cycleLength = 28,
        array $redatings = [],
    ): array {
        $lmp = self::effectiveLmp($method, $inputDate, $cycleLength);

        if ($redatings !== []) {
            $lmp = self::applyRedating($redatings);
        }

        return self::fromLmp($lmp, $today);
    }

    /**
     * Etkin SAT ve bugünden türetilen tüm alanlar.
     *
     * @return array{
     *     lmp_date: string, due_date: string, ga_days: int, week: int, day: int,
     *     display: string, trimester: int, days_left: int, progress: float,
     *     is_overdue: bool, needs_completion_prompt: bool
     * }
     */
    public static function fromLmp(string $lmp, string $today): array
    {
        $lmpDate = self::parse($lmp);
        $todayDate = self::parse($today);
        $dueDate = self::addDays($lmpDate, self::TOTAL_DAYS);

        $gaDays = self::diffDays($lmpDate, $todayDate);

        if ($gaDays < 0) {
            throw new InvalidArgumentException("Son adet tarihi gelecekte olamaz: {$lmp}");
        }

        return [
            'lmp_date'                => $lmpDate->format('Y-m-d'),
            'due_date'                => $dueDate,
            'ga_days'                 => $gaDays,
            'week'                    => intdiv($gaDays, 7),
            'day'                     => $gaDays % 7,
            'display'                 => intdiv($gaDays, 7) . 'h ' . ($gaDays % 7) . 'g',
            'trimester'               => self::trimester($gaDays),
            'days_left'               => self::diffDays($todayDate, self::parse($dueDate)),
            'progress'                => self::progress($gaDays),
            'is_overdue'              => $gaDays > self::TOTAL_DAYS,
            'needs_completion_prompt' => $gaDays > self::COMPLETION_PROMPT_DAYS,
        ];
    }

    /**
     * En son USG düzeltmesinden etkin SAT'ı türetir.
     *
     * @param  list<array{measured_on: string, ga_days_at_measure: int}>  $redatings
     */
    public static function applyRedating(array $redatings): string
    {
        usort($redatings, static fn (array $a, array $b): int => strcmp($a['measured_on'], $b['measured_on']));
        $latest = $redatings[array_key_last($redatings)];

        return self::addDays(self::parse($latest['measured_on']), -$latest['ga_days_at_measure']);
    }

    private static function trimester(int $gaDays): int
    {
        return match (true) {
            $gaDays < self::TRIMESTER_2_START => 1,
            $gaDays < self::TRIMESTER_3_START => 2,
            default                           => 3,
        };
    }

    /** Termin aşımında %100'de kilitlenir; ilerleme halkası geri sarmaz. */
    private static function progress(int $gaDays): float
    {
        return round(max(0.0, min(1.0, $gaDays / self::TOTAL_DAYS)), 4);
    }

    private static function parse(string $date): DateTimeImmutable
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) !== 1) {
            throw new InvalidArgumentException("Tarih Y-m-d biçiminde olmalı: {$date}");
        }

        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date, new DateTimeZone('UTC'));

        if ($parsed === false || $parsed->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException("Geçersiz tarih: {$date}");
        }

        return $parsed;
    }

    private static function addDays(DateTimeImmutable $date, int $days): string
    {
        return $date->modify(sprintf('%+d days', $days))->format('Y-m-d');
    }

    /**
     * İki takvim günü arasındaki fark. Her iki tarih de UTC gece yarısına
     * sabitlendiği için yaz saati geçişleri sonucu kaydırmaz.
     */
    private static function diffDays(DateTimeImmutable $from, DateTimeImmutable $to): int
    {
        return intdiv($to->getTimestamp() - $from->getTimestamp(), 86400);
    }
}
