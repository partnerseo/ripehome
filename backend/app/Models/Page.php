<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'content', 'meta_title', 'meta_description'];
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }
}
