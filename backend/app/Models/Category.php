<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use OptimizesImages;
    use HasTranslations;

    public array $translatable = ['name', 'description'];

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
        'slug_en', 'slug_ar', 'slug_ru', 'slug_de',
        'description',
        'image',
        'order',
        'is_active',
        'price',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        // Kategori fiyatı değişince tüm ürünlere otomatik uygula
        static::saved(function ($category) {
            if ($category->isDirty('price') && $category->price !== null) {
                $category->products()->update(['price' => $category->price]);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /** slug | slug_en/ar/ru/de herhangi biriyle çöz. */
    public function scopeSlug($query, string $slug)
    {
        return $query->where(function ($w) use ($slug) {
            $w->where('slug', $slug)
              ->orWhere('slug_en', $slug)->orWhere('slug_ar', $slug)
              ->orWhere('slug_ru', $slug)->orWhere('slug_de', $slug);
        });
    }
}
