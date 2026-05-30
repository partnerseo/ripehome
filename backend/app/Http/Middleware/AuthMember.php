<?php

namespace App\Http\Middleware;

use App\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMember
{
    public function __construct(private TokenService $tokens) {}

    public function handle(Request $request, Closure $next): Response
    {
        $raw = $this->extractToken($request);

        if (! $raw) {
            return response()->json(['message' => 'Kimlik doğrulama gerekli.'], 401);
        }

        $member = $this->tokens->verify($raw);

        if (! $member) {
            return response()->json(['message' => 'Geçersiz veya süresi dolmuş oturum.'], 401);
        }

        // Request'e member'ı ekle
        $request->attributes->set('member', $member);

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        // Authorization: Bearer <token>
        $bearer = $request->bearerToken();
        if ($bearer) return $bearer;

        // Cookie fallback
        return $request->cookie('ripehome_member_token');
    }
}
