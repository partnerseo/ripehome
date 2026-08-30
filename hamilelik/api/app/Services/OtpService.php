<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\OtpCodeMail;
use App\Models\OtpCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Tek kullanımlık giriş kodu üretimi ve doğrulaması.
 *
 * Kod hiçbir koşulda API yanıtında dönmez — hata ayıklama kolaylığı için bile.
 * Yerel geliştirmede koda log dosyasından ulaşılır.
 */
class OtpService
{
    public const CODE_TTL_MINUTES = 10;

    public const MAX_ATTEMPTS_PER_CODE = 5;

    /** Aynı e-posta için saatte kaç kod istenebilir. */
    private const EMAIL_LIMIT = 5;

    /** Aynı IP'den saatte kaç kod istenebilir. */
    private const IP_LIMIT = 20;

    private const WINDOW_SECONDS = 3600;

    /**
     * Kod üretir, hash'leyerek saklar ve e-posta ile gönderir.
     *
     * @return bool Sınır aşıldıysa false — çağıran taraf yine de aynı yanıtı
     *              döndürmelidir, aksi hâlde bu uç e-posta varlığını sızdırır.
     */
    public function request(string $email, ?string $ip): bool
    {
        $email = mb_strtolower(trim($email));

        // RateLimiter::hit sayacı gerçekten artırır; updateOrCreate ile yazılan
        // bir sayaç her istekte sıfırlanır ve sınır hiç devreye girmez.
        if (RateLimiter::tooManyAttempts($this->emailKey($email), self::EMAIL_LIMIT)) {
            return false;
        }

        if ($ip !== null && RateLimiter::tooManyAttempts($this->ipKey($ip), self::IP_LIMIT)) {
            return false;
        }

        RateLimiter::hit($this->emailKey($email), self::WINDOW_SECONDS);

        if ($ip !== null) {
            RateLimiter::hit($this->ipKey($ip), self::WINDOW_SECONDS);
        }

        // Aynı anda tek geçerli kod bulunsun: yeni kod eskisini geçersiz kılar.
        OtpCode::query()->where('email', $email)->whereNull('consumed_at')->update(['consumed_at' => now()]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
            'request_ip' => $ip,
        ]);

        $this->deliver($email, $code);

        return true;
    }

    /**
     * Kodu doğrular ve tek kullanımlık olarak tüketir.
     */
    public function verify(string $email, string $code): bool
    {
        $email = mb_strtolower(trim($email));

        $record = OtpCode::query()
            ->where('email', $email)
            ->usable()
            ->latest('id')
            ->first();

        if ($record === null) {
            return false;
        }

        if ($record->attempts >= self::MAX_ATTEMPTS_PER_CODE) {
            // Deneme hakkı bitti: kodu yak, kullanıcı yeni kod istesin.
            $record->update(['consumed_at' => now()]);

            return false;
        }

        $record->increment('attempts');

        if (! Hash::check($code, $record->code_hash)) {
            return false;
        }

        $record->update(['consumed_at' => now()]);

        RateLimiter::clear($this->emailKey($email));

        return true;
    }

    private function deliver(string $email, string $code): void
    {
        Mail::to($email)->send(new OtpCodeMail($code));

        // Yerel geliştirmede koda log'dan ulaşılır; üretimde kod hiçbir yere yazılmaz.
        if (! app()->environment('production')) {
            Log::debug("OTP {$email}: {$code}");
        }
    }

    private function emailKey(string $email): string
    {
        return 'otp:email:'.Str::lower($email);
    }

    private function ipKey(string $ip): string
    {
        return 'otp:ip:'.$ip;
    }
}
