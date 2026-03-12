<?php

namespace App\Listeners;

use App\Models\LoginLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $request = request();
        $ip = $request->ip();

        $country = null;
        $city = null;
        try {
            $geo = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}?fields=country,city"), true);
            if ($geo && isset($geo['country'])) {
                $country = $geo['country'];
                $city = $geo['city'] ?? null;
            }
        } catch (\Throwable $e) {
            // Konum bulunamazsa devam et
        }

        LoginLog::create([
            'user_id' => $event->user->id,
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
            'country' => $country,
            'city' => $city,
            'logged_in_at' => now(),
        ]);
    }
}
