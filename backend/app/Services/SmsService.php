<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * SMS gönderir. Netgsm entegrasyonu hazır olduğunda aktif edilir.
     */
    public function send(string $phone, string $message): bool
    {
        $username = config('services.netgsm.username');
        $password = config('services.netgsm.password');
        $header   = config('services.netgsm.header', 'RIPEHOME');

        // Netgsm henüz yapılandırılmamışsa log'a yaz, true dön (geliştirme modu)
        if (empty($username) || empty($password)) {
            Log::info('[SMS-STUB] Gönderilecek mesaj', [
                'telefon' => $phone,
                'mesaj'   => $message,
            ]);
            return true;
        }

        try {
            $url = 'https://api.netgsm.com.tr/sms/send/get/';
            $params = http_build_query([
                'usercode' => $username,
                'password' => $password,
                'gsmno'    => $phone,
                'message'  => $message,
                'msgheader' => $header,
                'dil'      => 'TR',
            ]);

            $response = file_get_contents($url . '?' . $params);

            // Netgsm başarı kodu: 00 veya 01
            $code = substr(trim($response), 0, 2);
            if (in_array($code, ['00', '01'])) {
                Log::info('[SMS] Gönderildi', ['telefon' => $phone]);
                return true;
            }

            Log::warning('[SMS] Netgsm hata kodu', ['kod' => $response, 'telefon' => $phone]);
            return false;
        } catch (\Exception $e) {
            Log::error('[SMS] Gönderim hatası', ['hata' => $e->getMessage(), 'telefon' => $phone]);
            return false;
        }
    }
}
