<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
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
    ];
}
