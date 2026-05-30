<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Setting extends Model
{
    use HasTranslations;

    public array $translatable = ['footer_text', 'brand_title', 'brand_subtitle', 'brand_description', 'brand_button_text'];
    protected $fillable = [
        'logo',
        'favicon',
        'phone',
        'email',
        'address',
        'facebook',
        'instagram',
        'twitter',
        'linkedin',
        'footer_text',
        'brand_title',
        'brand_subtitle',
        'brand_description',
        'brand_image',
        'brand_button_text',
        'brand_button_link',
        'video_url',
        'video_file',
        'video_title',
        'video_subtitle',
        'about_hero_image',
    ];
}
