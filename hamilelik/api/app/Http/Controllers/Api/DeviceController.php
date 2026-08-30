<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /** Cihazın bildirim jetonunu kaydeder; aynı jeton iki kez kaydedilmez. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'expo_push_token' => ['required', 'string', 'max:255'],
            'platform' => ['sometimes', 'string', 'max:16'],
            'timezone' => ['sometimes', 'timezone'],
        ]);

        $device = Device::firstOrNew(['expo_push_token' => $data['expo_push_token']]);

        // Sahiplik istemciden değil oturumdan gelir, bu yüzden toplu atamaya
        // açılmaz. Jeton başka bir hesapta kayıtlıysa sahibi güncellenir: aynı
        // cihazda başka bir kullanıcı giriş yapmış olabilir ve bildirimler eski
        // hesaba gitmemeli.
        $device->forceFill([
            'user_id' => $request->user()->id,
            'platform' => $data['platform'] ?? $device->platform,
            'timezone' => $data['timezone'] ?? $request->user()->timezone,
            'last_seen_at' => now(),
        ])->save();

        return response()->json(['data' => ['id' => $device->id]], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate(['expo_push_token' => ['required', 'string']]);

        $request->user()->devices()->where('expo_push_token', $data['expo_push_token'])->delete();

        return response()->json(null, 204);
    }
}
