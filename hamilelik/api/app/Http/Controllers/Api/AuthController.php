<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly OtpService $otp) {}

    /**
     * Giriş kodu ister.
     *
     * Yanıt her durumda aynıdır — e-posta kayıtlı olsun olmasın, sınır aşılmış
     * olsun olmasın. Farklı yanıt vermek kimlerin üye olduğunu sızdırır.
     */
    public function requestCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
        ]);

        $this->otp->request($data['email'], $request->ip());

        return response()->json([
            'message' => 'Kod gönderildi. E-postanızı kontrol edin.',
        ], 202);
    }

    /** Kodu doğrular ve erişim jetonu döndürür. */
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'code' => ['required', 'digits:6'],
            'timezone' => ['sometimes', 'timezone'],
            'locale' => ['sometimes', 'string', 'size:2'],
        ]);

        if (! $this->otp->verify($data['email'], $data['code'])) {
            return response()->json([
                'message' => 'Kod geçersiz veya süresi dolmuş.',
            ], 422);
        }

        $user = User::firstOrCreate(
            ['email' => mb_strtolower(trim($data['email']))],
            [
                'timezone' => $data['timezone'] ?? 'Europe/Istanbul',
                'locale' => $data['locale'] ?? 'tr',
            ],
        );

        // Saat dilimi hafta hesabının gün sınırını belirler; cihaz taşındıysa güncelle.
        if (isset($data['timezone']) && $user->timezone !== $data['timezone']) {
            $user->update(['timezone' => $data['timezone']]);
        }

        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return response()->json([
            'token' => $user->createToken('mobile')->plainTextToken,
            'user' => $this->userPayload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Çıkış yapıldı.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->userPayload($request->user())]);
    }

    /** @return array<string, mixed> */
    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'locale' => $user->locale,
            'timezone' => $user->timezone,
        ];
    }
}
