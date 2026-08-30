<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $scheduled_at
 * @property Carbon|null $window_start_on
 * @property Carbon|null $completed_at
 */
class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    public const SOURCE_AUTO = 'auto';

    public const SOURCE_MANUAL = 'manual';

    protected $fillable = [
        'title', 'category', 'description', 'is_optional',
        'window_start_week', 'window_end_week', 'window_start_on', 'window_end_on',
        'scheduled_at', 'location', 'doctor_name', 'notes',
        'reminder_at', 'completed_at', 'source',
    ];

    protected $attributes = ['source' => self::SOURCE_MANUAL, 'category' => 'visit'];

    protected function casts(): array
    {
        return [
            'is_optional' => 'boolean',
            'window_start_week' => 'integer',
            'window_end_week' => 'integer',
            'window_start_on' => 'date',
            'window_end_on' => 'date',
            'scheduled_at' => 'datetime',
            'reminder_at' => 'datetime',
            'reminded_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Pregnancy, $this> */
    public function pregnancy(): BelongsTo
    {
        return $this->belongsTo(Pregnancy::class);
    }

    /** @return BelongsTo<ScreeningTemplate, $this> */
    public function screeningTemplate(): BelongsTo
    {
        return $this->belongsTo(ScreeningTemplate::class);
    }

    public function scopeDue(Builder $query, Carbon $at): Builder
    {
        return $query
            ->whereNotNull('reminder_at')
            ->whereNull('reminded_at')
            ->whereNull('completed_at')
            ->where('reminder_at', '<=', $at);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
