<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\OtpRateLimit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    const OTP_TTL_MINUTES          = 5;
    const MAX_ATTEMPTS_PER_OTP     = 3;
    const MAX_FAILED_ATTEMPTS_LOCK = 5;
    const LOCK_MINUTES             = 10;
    const EMAIL_COOLDOWN_SECONDS   = 60;
    const EMAIL_MAX_DAILY          = 10;
    const IP_WINDOW_MINUTES        = 5;
    const IP_MAX_REQUESTS          = 5;
    const IP_LOCK_MINUTES          = 15;

    public function __construct(private ResendService $resend) {}

    public ?string $lastDebugCode = null;

    /**
     * OTP e-posta ile gönder.
     */
    public function send(string $email, string $ip, string $lang = 'en'): void
    {
        $this->checkIpLimit($ip);
        $this->checkEmailLimit($email);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Önceki kullanılmamış OTP'leri iptal et
        OtpCode::where('email', $email)
            ->where('kullanildi_mi', false)
            ->update(['kullanildi_mi' => true]);

        OtpCode::create([
            'email'           => $email,
            'telefon'         => null,
            'kod_hash'        => Hash::make($code),
            'son_kullanma'    => Carbon::now()->addMinutes(self::OTP_TTL_MINUTES),
            'ip_adresi'       => $ip,
            'kullanildi_mi'   => false,
            'deneme_sayisi'   => 0,
            'delivery_method' => 'email',
        ]);

        $this->incrementEmailLimit($email);
        $this->incrementIpLimit($ip);

        $this->resend->sendOtp($email, $code, self::OTP_TTL_MINUTES, $lang);

        $this->lastDebugCode = config('app.debug') ? $code : null;
    }

    /**
     * OTP doğrula.
     */
    public function verify(string $email, string $code): array
    {
        $lockKey   = "otp_lock_{$email}";
        $lockUntil = cache()->get($lockKey);
        if ($lockUntil && Carbon::parse($lockUntil)->isFuture()) {
            $left = max(1, (int) Carbon::parse($lockUntil)->diffInMinutes(now(), false) * -1);
            return ['ok' => false, 'error' => "Çok fazla yanlış deneme. {$left} dakika sonra tekrar deneyin.", 'locked' => true];
        }
        if ($lockUntil) {
            cache()->forget($lockKey);
        }

        $otp = OtpCode::where('email', $email)
            ->where('kullanildi_mi', false)
            ->where('son_kullanma', '>', Carbon::now())
            ->latest()
            ->first();

        if (! $otp) {
            return ['ok' => false, 'error' => 'Geçerli kod bulunamadı veya süresi doldu.', 'locked' => false, 'attempts_left' => null];
        }

        if (! Hash::check($code, $otp->kod_hash)) {
            $otp->increment('deneme_sayisi');

            if ($otp->deneme_sayisi >= self::MAX_ATTEMPTS_PER_OTP) {
                $otp->markUsed();
            }

            $failCount = (int) cache()->increment("otp_fail_{$email}");
            cache()->put("otp_fail_{$email}", $failCount, Carbon::now()->addMinutes(10));

            if ($failCount >= self::MAX_FAILED_ATTEMPTS_LOCK) {
                cache()->put($lockKey, Carbon::now()->addMinutes(self::LOCK_MINUTES)->toIso8601String(), Carbon::now()->addMinutes(self::LOCK_MINUTES));
                cache()->forget("otp_fail_{$email}");
                return ['ok' => false, 'error' => self::LOCK_MINUTES . ' dakika kilitlendi. Çok fazla yanlış deneme.', 'locked' => true];
            }

            $attemptsLeft = max(0, self::MAX_FAILED_ATTEMPTS_LOCK - $failCount);
            return ['ok' => false, 'error' => 'Hatalı kod.', 'locked' => false, 'attempts_left' => $attemptsLeft];
        }

        $otp->markUsed();
        cache()->forget("otp_fail_{$email}");

        return ['ok' => true, 'error' => null, 'locked' => false, 'attempts_left' => null];
    }

    // ─── Private ──────────────────────────────────────────────────────────────

    private function checkIpLimit(string $ip): void
    {
        if (cache()->get("ip_block_{$ip}")) {
            abort(429, 'IP adresiniz geçici olarak engellendi.');
        }

        $record = OtpRateLimit::where('anahtar', $ip)->where('tip', 'ip')->first();
        if ($record) {
            $windowEnd = $record->pencere_baslangici->addMinutes(self::IP_WINDOW_MINUTES);
            if ($windowEnd->isFuture() && $record->istek_sayisi >= self::IP_MAX_REQUESTS) {
                cache()->put("ip_block_{$ip}", true, Carbon::now()->addMinutes(self::IP_LOCK_MINUTES));
                abort(429, 'Çok fazla istek. ' . self::IP_LOCK_MINUTES . ' dakika sonra tekrar deneyin.');
            } elseif ($windowEnd->isPast()) {
                $record->update(['istek_sayisi' => 0, 'pencere_baslangici' => Carbon::now()]);
            }
        }
    }

    private function checkEmailLimit(string $email): void
    {
        $lastOtp = OtpCode::where('email', $email)->latest()->first();
        if ($lastOtp && $lastOtp->created_at->addSeconds(self::EMAIL_COOLDOWN_SECONDS)->isFuture()) {
            $wait = abs((int) Carbon::now()->diffInSeconds($lastOtp->created_at->addSeconds(self::EMAIL_COOLDOWN_SECONDS), false));
            abort(429, "Lütfen {$wait} saniye bekleyin.");
        }

        $todayCount = OtpCode::where('email', $email)->where('created_at', '>=', Carbon::today())->count();
        if ($todayCount >= self::EMAIL_MAX_DAILY) {
            abort(429, 'Günlük doğrulama kodu limitine ulaştınız. Lütfen yarın tekrar deneyin.');
        }
    }

    private function incrementEmailLimit(string $email): void
    {
        OtpRateLimit::updateOrCreate(
            ['anahtar' => $email, 'tip' => 'email'],
            ['istek_sayisi' => 0, 'pencere_baslangici' => Carbon::now()]
        );
        OtpRateLimit::where('anahtar', $email)->where('tip', 'email')->increment('istek_sayisi');
    }

    private function incrementIpLimit(string $ip): void
    {
        OtpRateLimit::updateOrCreate(
            ['anahtar' => $ip, 'tip' => 'ip'],
            ['istek_sayisi' => 0, 'pencere_baslangici' => Carbon::now()]
        );
        OtpRateLimit::where('anahtar', $ip)->where('tip', 'ip')->increment('istek_sayisi');
    }
}
