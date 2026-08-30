<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\MedicallyReviewed;
use Database\Factories\WeekContentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $week
 * @property string $locale
 * @property string $status
 */
class WeekContent extends Model
{
    /** @use HasFactory<WeekContentFactory> */
    use HasFactory, MedicallyReviewed;

    protected $fillable = [
        'week', 'locale', 'baby_size_label', 'baby_length_mm', 'baby_weight_g',
        'baby_body', 'mother_body', 'tips_body',
        'status', 'reviewed_by', 'reviewed_at', 'review_note', 'source_refs',
    ];

    protected $attributes = ['locale' => 'tr', 'status' => self::STATUS_DRAFT];

    protected function casts(): array
    {
        return [
            'week' => 'integer',
            'baby_length_mm' => 'integer',
            'baby_weight_g' => 'integer',
            'reviewed_at' => 'date',
            'source_refs' => 'array',
        ];
    }

    /** Onayın kapsadığı alanlar: biri değişirse onay geçersiz olur. */
    public function reviewableFields(): array
    {
        return ['baby_body', 'mother_body', 'tips_body', 'baby_size_label', 'baby_length_mm', 'baby_weight_g'];
    }
}
