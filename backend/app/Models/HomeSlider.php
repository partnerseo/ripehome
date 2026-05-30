<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class HomeSlider extends Model
{
    use OptimizesImages;
    use HasTranslations;

    public array $translatable = ['title', 'subtitle', 'button_text'];

    protected array $optimizableImages = [
        'image' => [
            'maxWidth' => 1920,
            'maxHeight' => 1080,
            'quality' => 85,
        ],
    ];

    protected $fillable = [
        'title',
        'subtitle',
        'button_text',
        'button_link',
        'image',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
