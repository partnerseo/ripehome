<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PregnancyResource;
use App\Models\PregnancyShare;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Gebeliğin salt-okunur paylaşımı.
 *
 * Paylaşım yalnızca okuma verir. Davet edilen kişi kayıt ekleyemez,
 * düzenleyemez, gebeliği kapatamaz — bunlar sahibin uçları ve sahiplik
 * kontrolünden geçer.
 */
class ShareController extends Controller
{
    /** Sahibin paylaşım listesi. */
    public function index(Request $request): JsonResponse
    {
        $pregnancy = $request->user()->pregnancies()->active()->first();

        if ($pregnancy === null) {
            return response()->json(['data' => []]);
        }

        return response()->json([
            'data' => $pregnancy->shares()->active()->get()->map($this->present(...)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $pregnancy = $request->user()->pregnancies()->active()->first();

        if ($pregnancy === null) {
            return response()->json([
                'message' => 'Paylaşmak için aktif bir gebelik kaydı gerekir.',
                'code' => 'no_active_pregnancy',
            ], 422);
        }

        $data = $request->validate(['email' => ['required', 'email:rfc', 'max:255']]);
        $email = mb_strtolower(trim($data['email']));

        if ($email === $request->user()->email) {
            return response()->json([
                'message' => 'Kendinizi davet edemezsiniz.',
                'code' => 'self_invite',
            ], 422);
        }

        // Aynı kişiye ikinci davet yeni kayıt açmaz; iptal edilmiş bir davet
        // yeniden gönderilirse canlanır ve yeni bir jeton alır.
        $share = $pregnancy->shares()->updateOrCreate(
            ['invited_email' => $email],
            ['token' => PregnancyShare::newToken(), 'revoked_at' => null, 'role' => PregnancyShare::ROLE_VIEWER],
        );

        return response()->json(['data' => $this->present($share)], 201);
    }

    /** Daveti kabul eder. Jeton davet e-postasındaki bağlantıdan gelir. */
    public function accept(Request $request): JsonResponse
    {
        $data = $request->validate(['token' => ['required', 'string', 'max:64']]);

        $share = PregnancyShare::query()->active()->where('token', $data['token'])->first();

        if ($share === null) {
            return response()->json([
                'message' => 'Bu davet geçersiz veya iptal edilmiş.',
                'code' => 'invalid_invite',
            ], 404);
        }

        // Davet e-postası sahibinden başkası kabul edemez: bağlantı
        // paylaşılsa bile erişim davet edilen kişiye kalır.
        if ($share->invited_email !== $request->user()->email) {
            return response()->json([
                'message' => 'Bu davet başka bir e-posta adresi için gönderilmiş.',
                'code' => 'invite_email_mismatch',
            ], 403);
        }

        $share->update(['user_id' => $request->user()->id, 'accepted_at' => now()]);

        return response()->json(['data' => $this->present($share)]);
    }

    /** Sahibin daveti iptal etmesi. */
    public function destroy(Request $request, PregnancyShare $share): JsonResponse
    {
        abort_unless($share->pregnancy->user_id === $request->user()->id, 404);

        $share->update(['revoked_at' => now()]);

        return response()->json(null, 204);
    }

    /** Bana paylaşılan gebelikler — salt okunur. */
    public function sharedWithMe(Request $request): JsonResponse
    {
        $shares = $request->user()->sharedWithMe()
            ->active()
            ->whereNotNull('accepted_at')
            ->with('pregnancy.user')
            ->get()
            ->filter(fn (PregnancyShare $s): bool => $s->pregnancy !== null && $s->pregnancy->isActive())
            ->map(fn (PregnancyShare $s): array => [
                'share_id' => $s->id,
                'owner_email' => $s->pregnancy->user->email,
                'pregnancy' => new PregnancyResource($s->pregnancy),
            ])
            ->values();

        return response()->json(['data' => $shares]);
    }

    /** @return array<string, mixed> */
    private function present(PregnancyShare $share): array
    {
        return [
            'id' => $share->id,
            'invited_email' => $share->invited_email,
            'role' => $share->role,
            'accepted_at' => $share->accepted_at?->toIso8601String(),
            // Jeton yalnızca sahibe gösterilir: daveti elden iletebilsin.
            'token' => $share->token,
        ];
    }
}
