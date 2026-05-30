<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['tr', 'en', 'ar', 'ru', 'de'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('locale', 'tr');

        if (in_array($locale, self::SUPPORTED_LOCALES)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
