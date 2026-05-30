<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class FeaturedSection extends Model
{
    use OptimizesImages;
    use HasTranslations;

    public array $translatable = ['title', 'description', 'button_text'];

    protected array $optimizableImages = [
        'image' => [
            'maxWidth' => 1200,
            'maxHeight' => 1200,
            'quality' => 82,
        ],
    ];

    protected $fillable = [
        'title',
        'description',
        'image',
        'icon',
        'link',
        'button_text',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
