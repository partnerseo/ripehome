<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    /**
     * Tek bir görseli optimize et.
     */
    public function optimize(string $path, int $maxWidth = 1600, int $maxHeight = 1600, int $quality = 80): bool
    {
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            Log::warning("ImageOptimizer: Dosya bulunamadı: {$path}");
            return false;
        }

        $fullPath = $disk->path($path);

        // Dosya boyutu - öncesi
        $sizeBefore = filesize($fullPath);

        try {
            $optimized = $this->optimizeWithImagick($fullPath, $maxWidth, $maxHeight, $quality)
                ?? $this->optimizeWithConvert($fullPath, $maxWidth, $maxHeight, $quality)
                ?? $this->optimizeWithCliTools($fullPath, $quality);

            if (!$optimized) {
                Log::info("ImageOptimizer: Hiçbir araç kullanılamadı, görsel olduğu gibi bırakıldı: {$path}");
                return false;
            }

            clearstatcache(true, $fullPath);
            $sizeAfter = filesize($fullPath);
            $savings = $sizeBefore > 0 ? round((1 - $sizeAfter / $sizeBefore) * 100, 1) : 0;

            Log::info("ImageOptimizer: {$path} - Öncesi: " . $this->formatBytes($sizeBefore) .
                " → Sonrası: " . $this->formatBytes($sizeAfter) . " ({$savings}% tasarruf)");

            return true;
        } catch (\Throwable $e) {
            Log::error("ImageOptimizer: Görsel optimize edilemedi: {$path} - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Birden fazla görseli optimize et.
     */
    public function optimizeMany(array $paths, int $maxWidth = 1600, int $maxHeight = 1600, int $quality = 80): array
    {
        $results = [];

        foreach ($paths as $path) {
            $results[$path] = $this->optimize($path, $maxWidth, $maxHeight, $quality);
        }

        return $results;
    }

    /**
     * Imagick extension ile optimize et.
     */
    protected function optimizeWithImagick(string $fullPath, int $maxWidth, int $maxHeight, int $quality): ?bool
    {
        if (!extension_loaded('imagick')) {
            return null;
        }

        try {
            $imagick = new \Imagick($fullPath);

            // Profil bilgilerini temizle (EXIF vb.)
            $imagick->stripImage();

            // Boyut küçültme
            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();

            if ($width > $maxWidth || $height > $maxHeight) {
                $imagick->thumbnailImage($maxWidth, $maxHeight, true);
            }

            // Format bazlı sıkıştırma
            $format = strtolower($imagick->getImageFormat());

            if (in_array($format, ['jpeg', 'jpg'])) {
                $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
                $imagick->setImageCompressionQuality($quality);
                $imagick->setSamplingFactors(['2x2', '1x1', '1x1']);
                $imagick->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
            } elseif ($format === 'png') {
                $imagick->setImageCompressionQuality($quality);
            }

            $imagick->writeImage($fullPath);
            $imagick->destroy();

            return true;
        } catch (\Throwable $e) {
            Log::debug("ImageOptimizer: Imagick başarısız, fallback denenecek: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ImageMagick convert komutu ile optimize et.
     */
    protected function optimizeWithConvert(string $fullPath, int $maxWidth, int $maxHeight, int $quality): ?bool
    {
        $convertBin = $this->findBinary('convert');

        if (!$convertBin) {
            return null;
        }

        try {
            $dimensions = "{$maxWidth}x{$maxHeight}>";
            $escapedPath = escapeshellarg($fullPath);

            $command = "{$convertBin} {$escapedPath} -resize " . escapeshellarg($dimensions) .
                " -strip -quality {$quality} -interlace Plane {$escapedPath}";

            exec($command, $output, $returnCode);

            return $returnCode === 0 ? true : null;
        } catch (\Throwable $e) {
            Log::debug("ImageOptimizer: convert komutu başarısız: " . $e->getMessage());
            return null;
        }
    }

    /**
     * jpegoptim/pngquant CLI araçları ile optimize et.
     */
    protected function optimizeWithCliTools(string $fullPath, int $quality): ?bool
    {
        $mimeType = mime_content_type($fullPath);
        $optimized = false;

        if ($mimeType === 'image/jpeg') {
            $jpegoptim = $this->findBinary('jpegoptim');
            if ($jpegoptim) {
                $escapedPath = escapeshellarg($fullPath);
                exec("{$jpegoptim} --strip-all --max={$quality} {$escapedPath}", $output, $returnCode);
                $optimized = $returnCode === 0;
            }
        } elseif ($mimeType === 'image/png') {
            $pngquant = $this->findBinary('pngquant');
            if ($pngquant) {
                $escapedPath = escapeshellarg($fullPath);
                $minQuality = max(0, $quality - 20);
                exec("{$pngquant} --force --quality={$minQuality}-{$quality} --output {$escapedPath} -- {$escapedPath}", $output, $returnCode);
                $optimized = $returnCode === 0;
            }
        }

        return $optimized ?: null;
    }

    /**
     * Sistem binary'sini bul.
     */
    protected function findBinary(string $name): ?string
    {
        $paths = [
            "/usr/bin/{$name}",
            "/usr/local/bin/{$name}",
            "/opt/homebrew/bin/{$name}",
        ];

        foreach ($paths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        // which komutu ile dene
        $result = trim(shell_exec("which {$name} 2>/dev/null") ?? '');
        return !empty($result) && is_executable($result) ? $result : null;
    }

    /**
     * Byte boyutunu okunabilir formata çevir.
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, 2) . ' ' . $units[$index];
    }
}
