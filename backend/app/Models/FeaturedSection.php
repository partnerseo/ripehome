<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedSection extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'icon',
        'link',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
