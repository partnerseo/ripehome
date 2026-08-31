<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ContractionSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractionSession extends Model
{
    /** @use HasFactory<ContractionSessionFactory> */
    use HasFactory;

    /** 5-1-1: 5 dakikada bir gelen, 1 dakika süren, 1 saattir devam eden. */
    public const RULE_INTERVAL_SEC = 5 * 60;

    public const RULE_DURATION_SEC = 60;

    public const RULE_WINDOW_SEC = 60 * 60;

    protected $fillable = ['started_at', 'ended_at', 'client_uuid'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'ended_at' => 'datetime'];
    }

    /** @return BelongsTo<Pregnancy, $this> */
    public function pregnancy(): BelongsTo
    {
        return $this->belongsTo(Pregnancy::class);
    }

    /** @return HasMany<Contraction, $this> */
    public function contractions(): HasMany
    {
        return $this->hasMany(Contraction::class)->orderBy('started_at');
    }

    /**
     * 5-1-1 kuralı sağlandı mı?
     *
     * Son bir saatteki kasılmalara bakılır: ortalama aralık 5 dakika veya altı,
     * ortalama süre 1 dakika veya üzeri, ve pencere gerçekten bir saati bulmuş.
     * Sağlanırsa arayüz hastaneye başvurma yönlendirmesi gösterir.
     */
    public function meetsFiveOneOne(): bool
    {
        $recent = $this->contractions
            ->filter(fn (Contraction $c): bool => $c->started_at->diffInSeconds(now()) <= self::RULE_WINDOW_SEC)
            ->values();

        if ($recent->count() < 2) {
            return false;
        }

        $span = $recent->first()->started_at->diffInSeconds($recent->last()->started_at);

        if ($span < self::RULE_WINDOW_SEC) {
            return false;
        }

        $intervals = $recent->skip(1)->pluck('interval_sec')->filter();

        if ($intervals->isEmpty()) {
            return false;
        }

        return $intervals->avg() <= self::RULE_INTERVAL_SEC
            && $recent->avg('duration_sec') >= self::RULE_DURATION_SEC;
    }
}
