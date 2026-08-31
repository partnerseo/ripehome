<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\KickSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KickSession extends Model
{
    /** @use HasFactory<KickSessionFactory> */
    use HasFactory;

    /** Cardiff yöntemi: 10 hareket sayılır. */
    public const TARGET = 10;

    /** Bu süre içinde hedefe ulaşılamazsa doktora başvurulmalı. */
    public const LIMIT_MINUTES = 120;

    protected $fillable = ['started_at', 'ended_at', 'target_count', 'kick_count', 'client_uuid'];

    protected $attributes = ['target_count' => self::TARGET, 'kick_count' => 0];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'ended_at' => 'datetime'];
    }

    /** @return BelongsTo<Pregnancy, $this> */
    public function pregnancy(): BelongsTo
    {
        return $this->belongsTo(Pregnancy::class);
    }

    /** @return HasMany<KickEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(KickEvent::class);
    }

    public function reachedTarget(): bool
    {
        return $this->kick_count >= $this->target_count;
    }

    public function durationMinutes(): ?int
    {
        return $this->ended_at === null ? null : (int) $this->started_at->diffInMinutes($this->ended_at);
    }

    /**
     * Hedefe ulaşılamadan süre dolduysa yönlendirme gerekir.
     * Sayı yorumlanmaz, "normaldir" denmez — doktora başvurulması söylenir.
     */
    public function needsUrgentCare(): bool
    {
        return ! $this->reachedTarget()
            && $this->ended_at !== null
            && $this->durationMinutes() >= self::LIMIT_MINUTES;
    }
}
