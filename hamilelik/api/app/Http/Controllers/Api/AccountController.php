<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consent;
use App\Services\AccountDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private readonly AccountDataService $data) {}

    /** Açık rıza kaydı. Sürüm değişirse yeniden istenir. */
    public function consent(Request $request): JsonResponse
    {
        $user = $request->user();

        $consent = $user->consents()->updateOrCreate(
            ['version' => Consent::CURRENT_VERSION],
            ['accepted_at' => now(), 'withdrawn_at' => null, 'ip_address' => $request->ip()],
        );

        return response()->json([
            'data' => [
                'version' => $consent->version,
                'accepted_at' => $consent->accepted_at->toIso8601String(),
            ],
        ], 201);
    }

    public function exportData(Request $request): JsonResponse
    {
        return response()->json($this->data->export($request->user()));
    }

    /**
     * Hesabı kalıcı olarak siler.
     *
     * Onay için e-postanın birebir yazılması istenir: bu ekran geri alınamaz
     * bir işlem yapıyor ve yanlışlıkla dokunulacak bir düğme olmamalı.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'confirm_email' => ['required', 'string'],
        ]);

        if (mb_strtolower(trim($request->string('confirm_email')->toString())) !== $user->email) {
            return response()->json([
                'message' => 'Onaylamak için hesabınızın e-posta adresini yazın.',
                'code' => 'confirmation_mismatch',
            ], 422);
        }

        $this->data->delete($user);

        return response()->json(['message' => 'Hesabınız ve tüm verileriniz silindi.']);
    }
}
