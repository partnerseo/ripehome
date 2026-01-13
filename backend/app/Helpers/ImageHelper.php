<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Storage'daki görsel için tam URL oluştur
     * Otomatik olarak ASSET_URL veya APP_URL kullanır
     */
    public static function getStorageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // Eğer zaten tam URL ise olduğu gibi dön
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Base URL'i al (ASSET_URL varsa onu, yoksa APP_URL kullan)
        $baseUrl = config('app.asset_url') ?: config('app.url');
        
        // Temizle ve birleştir
        return rtrim($baseUrl, '/') . '/storage/' . ltrim($path, '/');
    }

    /**
     * Birden fazla görsel için URL oluştur
     */
    public static function getStorageUrls(array $paths): array
    {
        return array_map(fn($path) => self::getStorageUrl($path), $paths);
    }
}


