<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name','contact_person','position',
        'content','avatar','company_logo',
        'rating','order_index','is_active',
        'content_en','content_ar','content_ru','content_de',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
        'order_index' => 'integer',
    ];

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeOrdered($query) { return $query->orderBy('order_index'); }
}
