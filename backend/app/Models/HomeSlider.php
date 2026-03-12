<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Illuminate\Database\Eloquent\Model;

class HomeSlider extends Model
{
    use OptimizesImages;

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
