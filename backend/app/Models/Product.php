<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use OptimizesImages;

    protected array $optimizableImages = [
        'images' => [
            'maxWidth' => 1600,
            'maxHeight' => 1600,
            'quality' => 80,
            'multiple' => true,
        ],
    ];

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'images',
        'category_id',
        'stock',
        'min_order',
        'production_time',
        'features',
        'meta_title',
        'meta_description',
        'is_active',
        'is_featured',
        'order',
    ];

    protected $casts = [
        'images' => 'array',
        'features' => 'array',
        'price' => 'decimal:2',
        'stock' => 'integer',
        'min_order' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
