<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MemberToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class TokenService
{
    // 30 → 7 gün (kısa TTL + kullanıcı yeniden giriş yapar)
    const TOKEN_TTL_DAYS = 7;

    /**
     * Yeni token üretir. [ 'token' => string, 'expires_at' => ISO8601 ] döner.
     * Token = 16 char prefix (DB'de plain) + 112 char secret (DB'de hash).
     * Lookup O(1): prefix'e göre tek satır bul, sonra hash doğrula.
     */
    public function create(Member $member, string $ip, string $userAgent): array
    {
        // Tek cihaz politikası — eski tokenları temizle
        $member->tokens()->delete();

        $prefix = bin2hex(random_bytes(8));   // 16 hex char
        $secret = bin2hex(random_bytes(56));  // 112 hex char
        $raw    = $prefix . $secret;          // 128 char toplam

        $expires = Carbon::now()->addDays(self::TOKEN_TTL_DAYS);

        MemberToken::create([
            'member_id'    => $member->id,
            'token_prefix' => $prefix,
            'token_hash'   => Hash::make($raw),
            'son_kullanma' => $expires,
            'ip'           => $ip,
            'user_agent'   => substr($userAgent, 0, 500),
        ]);

        return [
            'token'      => $raw,
            'expires_at' => $expires->toIso8601String(),
        ];
    }

    /**
     * Token doğrular → Member veya null.
     * Prefix ile tek satır bulur, sonra tam hash'i kontrol eder.
     */
    public function verify(string $raw): ?Member
    {
        if (strlen($raw) < 16) return null;

        $prefix = substr($raw, 0, 16);

        // Prefix'e göre tek satır — O(1) index lookup
        $token = MemberToken::where('token_prefix', $prefix)
            ->where('son_kullanma', '>', Carbon::now())
            ->with('member')
            ->first();

        if (! $token) {
            // Eski prefix'siz token fallback (migration öncesi oturumlar)
            return $this->verifyLegacy($raw);
        }

        if (! Hash::check($raw, $token->token_hash)) {
            return null;
        }

        if (! $token->member || ! $token->member->isActive()) {
            return null;
        }

        return $token->member;
    }

    /**
     * Token'ı DB'den sil (logout).
     */
    public function revoke(string $raw): void
    {
        if (strlen($raw) >= 16) {
            $prefix = substr($raw, 0, 16);
            $token = MemberToken::where('token_prefix', $prefix)
                ->where('son_kullanma', '>', Carbon::now())
                ->first();
            if ($token && Hash::check($raw, $token->token_hash)) {
                $token->delete();
                return;
            }
        }

        // Legacy fallback
        $tokens = MemberToken::where('son_kullanma', '>', Carbon::now())->get();
        foreach ($tokens as $token) {
            if (Hash::check($raw, $token->token_hash)) {
                $token->delete();
                return;
            }
        }
    }

    /**
     * Üyenin tüm tokenlarını iptal et.
     */
    public function revokeAll(Member $member): void
    {
        $member->tokens()->delete();
    }

    // ─── Eski token'lar için yavaş fallback (zamanla eriyecek) ───────────────

    private function verifyLegacy(string $raw): ?Member
    {
        $tokens = MemberToken::whereNull('token_prefix')
            ->where('son_kullanma', '>', Carbon::now())
            ->with('member')
            ->get();

        foreach ($tokens as $token) {
            if (Hash::check($raw, $token->token_hash)) {
                if (! $token->member || ! $token->member->isActive()) {
                    return null;
                }
                return $token->member;
            }
        }

        return null;
    }
}
