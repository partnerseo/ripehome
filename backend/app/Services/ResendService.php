<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ResendService
{
    private string $apiKey;
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->apiKey    = (string) config('services.resend.key', '');
        $this->fromEmail = 'noreply@ripehome.com.tr';
        $this->fromName  = 'Ripe Home';
    }

    /**
     * OTP e-postası gönder.
     * Stub mod: RESEND_KEY tanımlı değilse sadece log yazar.
     */
    public function sendOtp(string $to, string $code, int $ttlMinutes, string $lang = 'en'): bool
    {
        $subject = $this->subject($lang);
        $html    = $this->template($code, $ttlMinutes, $lang);

        if (empty($this->apiKey)) {
            Log::info("[RESEND-STUB] To: {$to} | Subject: {$subject} | Code: {$code}");
            return true;
        }

        $payload = [
            'from'    => "{$this->fromName} <{$this->fromEmail}>",
            'to'      => [$to],
            'subject' => $subject,
            'html'    => $html,
        ];

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        Log::error("[RESEND] E-posta gönderilemedi. To: {$to} | HTTP: {$httpCode} | Response: {$response}");
        throw new \RuntimeException('E-posta gönderilemedi. Lütfen tekrar deneyin.');
    }

    // ─── Private ──────────────────────────────────────────────────────────────

    private function subject(string $lang): string
    {
        return match ($lang) {
            'tr' => 'Ripe Home Doğrulama Kodunuz',
            'de' => 'Ihr Ripe Home Verifizierungscode',
            'ru' => 'Ваш код подтверждения Ripe Home',
            'ar' => 'رمز التحقق الخاص بك - Ripe Home',
            default => 'Your Ripe Home Verification Code',
        };
    }

    private function template(string $code, int $ttl, string $lang): string
    {
        [$title, $desc, $validFor, $ignore] = match ($lang) {
            'tr' => [
                'Doğrulama Kodunuz',
                'Ripe Home toptan üyelik sistemine giriş için doğrulama kodunuz:',
                "{$ttl} dakika geçerlidir.",
                'Bu isteği siz yapmadıysanız bu e-postayı görmezden gelebilirsiniz.',
            ],
            'de' => [
                'Ihr Verifizierungscode',
                'Ihr Verifizierungscode für das Ripe Home Großhandels-Mitgliedschaftssystem:',
                "Gültig für {$ttl} Minuten.",
                'Wenn Sie diese Anfrage nicht gestellt haben, ignorieren Sie diese E-Mail.',
            ],
            'ru' => [
                'Ваш код подтверждения',
                'Ваш код подтверждения для системы оптовых членства Ripe Home:',
                "Действителен {$ttl} минут.",
                'Если вы не делали этот запрос, проигнорируйте это письмо.',
            ],
            'ar' => [
                'رمز التحقق الخاص بك',
                'رمز التحقق الخاص بك لنظام العضوية بالجملة في Ripe Home:',
                "صالح لمدة {$ttl} دقائق.",
                'إذا لم تطلب هذا، يمكنك تجاهل هذا البريد الإلكتروني.',
            ],
            default => [
                'Your Verification Code',
                'Your verification code for the Ripe Home wholesale membership system:',
                "Valid for {$ttl} minutes.",
                'If you did not request this, you can safely ignore this email.',
            ],
        };

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#F8F6F3;font-family:Georgia,serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F8F6F3;padding:40px 20px;">
    <tr><td align="center">
      <table width="100%" style="max-width:480px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#1a1a1a,#333);padding:32px;text-align:center;">
            <p style="margin:0;color:#C4A96A;font-size:11px;letter-spacing:3px;text-transform:uppercase;font-family:Arial,sans-serif;">RIPE HOME</p>
            <p style="margin:6px 0 0;color:rgba(255,255,255,0.5);font-size:11px;font-family:Arial,sans-serif;">ripehome.com.tr</p>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:40px 36px;">
            <h2 style="margin:0 0 12px;color:#1a1a1a;font-size:22px;font-weight:normal;">{$title}</h2>
            <p style="margin:0 0 28px;color:#666;font-size:14px;line-height:1.6;font-family:Arial,sans-serif;">{$desc}</p>

            <!-- Code Box -->
            <div style="background:#F8F6F3;border:2px dashed #C4A96A;border-radius:12px;padding:24px;text-align:center;margin:0 0 28px;">
              <span style="font-family:monospace,Arial;font-size:42px;font-weight:bold;letter-spacing:12px;color:#1a1a1a;">{$code}</span>
            </div>

            <p style="margin:0 0 8px;color:#999;font-size:12px;font-family:Arial,sans-serif;">⏱ {$validFor}</p>
            <p style="margin:0;color:#bbb;font-size:11px;font-family:Arial,sans-serif;">{$ignore}</p>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#F8F6F3;padding:20px 36px;border-top:1px solid #eee;">
            <p style="margin:0;color:#bbb;font-size:11px;font-family:Arial,sans-serif;text-align:center;">
              © 2025 Ripe Home · Denizli, Türkiye
            </p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }
}
