<?php

namespace App\Traits;

use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\Log;

trait OptimizesImages
{
    public static function bootOptimizesImages(): void
    {
        static::saved(function ($model) {
            $model->optimizeModelImages();
        });
    }

    /**
     * Model'deki tüm optimize edilecek görselleri işle.
     */
    public function optimizeModelImages(): void
    {
        if (!property_exists($this, 'optimizableImages') || empty($this->optimizableImages)) {
            return;
        }

        $optimizer = app(ImageOptimizer::class);

        foreach ($this->optimizableImages as $field => $config) {
            $maxWidth = $config['maxWidth'] ?? 1600;
            $maxHeight = $config['maxHeight'] ?? 1600;
            $quality = $config['quality'] ?? 80;
            $multiple = $config['multiple'] ?? false;

            $value = $this->getAttribute($field);

            if (empty($value)) {
                continue;
            }

            try {
                if ($multiple && is_array($value)) {
                    $optimizer->optimizeMany($value, $maxWidth, $maxHeight, $quality);
                } elseif (is_string($value)) {
                    $optimizer->optimize($value, $maxWidth, $maxHeight, $quality);
                }
            } catch (\Throwable $e) {
                Log::error("OptimizesImages: {$field} alanı optimize edilemedi - " . $e->getMessage());
            }
        }
    }
}
