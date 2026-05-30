<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class FeaturedProduct extends Model
{
    use OptimizesImages;
    use HasTranslations;

    public array $translatable = ['category_label', 'title', 'description', 'button_text'];

    protected array $optimizableImages = [
        'image' => [
            'maxWidth' => 1200,
            'maxHeight' => 1200,
            'quality' => 82,
        ],
    ];

    protected $fillable = [
        'category_label',
        'title',
        'description',
        'image',
        'tags',
        'button_text',
        'button_link',
        'order',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
    ];
}













