<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\MedicallyReviewed;
use Database\Factories\ScreeningTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $code
 * @property int $week_start
 * @property int $week_end
 * @property string $status
 */
class ScreeningTemplate extends Model
{
    /** @use HasFactory<ScreeningTemplateFactory> */
    use HasFactory, MedicallyReviewed;

    public const CATEGORIES = ['usg', 'lab', 'vaccine', 'visit'];

    protected $fillable = [
        'code', 'locale', 'country', 'name', 'description', 'category',
        'week_start', 'week_end', 'is_optional', 'sort',
        'status', 'reviewed_by', 'reviewed_at', 'review_note', 'source_refs',
    ];

    protected $attributes = ['locale' => 'tr', 'country' => 'TR', 'status' => self::STATUS_DRAFT];

    protected function casts(): array
    {
        return [
            'week_start' => 'integer',
            'week_end' => 'integer',
            'is_optional' => 'boolean',
            'sort' => 'integer',
            'reviewed_at' => 'date',
            'source_refs' => 'array',
        ];
    }

    /**
     * Hafta aralığı ve adı onayın kapsamındadır: yanlış bir hafta, kaçırılmış
     * bir tarama demektir.
     */
    public function reviewableFields(): array
    {
        return ['name', 'description', 'category', 'week_start', 'week_end', 'is_optional'];
    }
}
