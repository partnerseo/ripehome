<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Illuminate\Database\Eloquent\Model;

class FeaturedSection extends Model
{
    use OptimizesImages;

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
