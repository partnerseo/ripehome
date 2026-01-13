<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedProduct extends Model
{
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












