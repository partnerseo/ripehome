<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
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
        'name',
        'slug',
        'description',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
