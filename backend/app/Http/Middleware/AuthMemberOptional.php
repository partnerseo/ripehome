<?php

namespace App\Http\Middleware;

use App\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMemberOptional
{
    public function __construct(private TokenService $tokens) {}

    public function handle(Request $request, Closure $next): Response
    {
        $raw = $request->bearerToken() ?? $request->cookie('ripehome_member_token');

        if ($raw) {
            $member = $this->tokens->verify($raw);
            if ($member) {
                $request->attributes->set('member', $member);
            }
        }

        return $next($request);
    }
}
