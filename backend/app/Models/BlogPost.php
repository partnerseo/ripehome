<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','content','excerpt','image',
        'slug_en','slug_ar','slug_ru','slug_de',
        'published_at','is_published','views',
        'title_en','content_en','excerpt_en',
        'title_ar','content_ar','excerpt_ar',
        'title_ru','content_ru','excerpt_ru',
        'title_de','content_de','excerpt_de',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'views' => 'integer',
    ];

    public function scopePublished($query) {
        return $query->where('is_published', true);
    }

    /** slug | slug_en/ar/ru/de herhangi biriyle çöz. */
    public function scopeSlug($query, string $slug) {
        return $query->where(function ($w) use ($slug) {
            $w->where('slug', $slug)
              ->orWhere('slug_en', $slug)->orWhere('slug_ar', $slug)
              ->orWhere('slug_ru', $slug)->orWhere('slug_de', $slug);
        });
    }
}
