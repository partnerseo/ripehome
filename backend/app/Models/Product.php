<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use OptimizesImages;
    use HasTranslations;

    public array $translatable = ['name', 'description', 'short_description', 'meta_title', 'meta_description'];

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
        'slug_en', 'slug_ar', 'slug_ru', 'slug_de',
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

    /** slug | slug_en/ar/ru/de herhangi biriyle çöz (eski TR linkler çalışır). */
    public function scopeSlug($query, string $slug)
    {
        return $query->where(function ($w) use ($slug) {
            $w->where('slug', $slug)
              ->orWhere('slug_en', $slug)->orWhere('slug_ar', $slug)
              ->orWhere('slug_ru', $slug)->orWhere('slug_de', $slug);
        });
    }
}
