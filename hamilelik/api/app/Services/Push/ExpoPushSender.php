<?php

declare(strict_types=1);

namespace App\Services\Push;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushSender implements PushSender
{
    private const ENDPOINT = 'https://exp.host/--/api/v2/push/send';

    /** Expo tek istekte en fazla 100 mesaj kabul eder. */
    private const CHUNK = 100;

    public function send(array $tokens, PushMessage $message): void
    {
        if ($tokens === []) {
            return;
        }

        foreach (array_chunk($tokens, self::CHUNK) as $chunk) {
            $payload = array_map(fn (string $token): array => [
                'to' => $token,
                'title' => $message->title,
                'body' => $message->body,
                'data' => $message->data,
                'sound' => 'default',
            ], $chunk);

            $response = Http::acceptJson()->asJson()->post(self::ENDPOINT, $payload);

            if ($response->failed()) {
                // Bildirim gönderilemedi diye istek zinciri kırılmamalı; kayıt
                // düşülür, bir sonraki planlayıcı turunda tekrar denenir.
                Log::warning('Expo push gönderilemedi', [
                    'status' => $response->status(),
                    'count' => count($chunk),
                ]);
            }
        }
    }
}
