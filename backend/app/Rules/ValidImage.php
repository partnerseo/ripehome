<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidImage implements ValidationRule
{
    protected array $allowedTypes = [
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_GIF,
        IMAGETYPE_WEBP,
    ];

    protected array $typeNames = [
        IMAGETYPE_JPEG => 'JPEG',
        IMAGETYPE_PNG  => 'PNG',
        IMAGETYPE_GIF  => 'GIF',
        IMAGETYPE_WEBP => 'WebP',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Dosya yolunu belirle
        $path = is_object($value) && method_exists($value, 'getRealPath')
            ? $value->getRealPath()
            : (string) $value;

        if (!file_exists($path)) {
            $fail('Görsel dosyası bulunamadı.');
            return;
        }

        // getimagesize ile doğrulama
        $imageInfo = @getimagesize($path);

        if ($imageInfo === false) {
            $fail('Yüklenen dosya geçerli bir görsel değil.');
            return;
        }

        // Desteklenen format kontrolü
        $imageType = $imageInfo[2];

        if (!in_array($imageType, $this->allowedTypes, true)) {
            $allowedNames = implode(', ', array_values($this->typeNames));
            $fail("Desteklenmeyen görsel formatı. İzin verilen formatlar: {$allowedNames}.");
            return;
        }

        // GD ile decode testi
        if (extension_loaded('gd')) {
            $decoded = $this->testDecode($path, $imageType);

            if (!$decoded) {
                $fail('Görsel dosyası bozuk veya okunamıyor.');
                return;
            }
        }
    }

    /**
     * GD kütüphanesi ile görseli decode etmeye çalış.
     */
    protected function testDecode(string $path, int $imageType): bool
    {
        try {
            $image = match ($imageType) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
                IMAGETYPE_PNG  => @imagecreatefrompng($path),
                IMAGETYPE_GIF  => @imagecreatefromgif($path),
                IMAGETYPE_WEBP => @imagecreatefromwebp($path),
                default        => false,
            };

            if ($image !== false) {
                imagedestroy($image);
                return true;
            }

            return false;
        } catch (\Throwable) {
            return false;
        }
    }
}
