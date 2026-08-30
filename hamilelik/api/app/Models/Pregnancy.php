<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PregnancyFactory;
use Hamilelik\Engine\GestationalAge;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $method
 * @property Carbon $input_date
 * @property int $cycle_length
 * @property Carbon $lmp_date
 * @property Carbon $due_date
 * @property string $status
 */
class Pregnancy extends Model
{
    /** @use HasFactory<PregnancyFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_ENDED = 'ended';

    /** Gebeliğin nasıl kapandığı. Kayıp hâlinde arayüz hafta/geri sayım göstermez. */
    public const REASONS = ['birth', 'loss', 'other'];

    protected $fillable = ['method', 'input_date', 'cycle_length', 'baby_count'];

    /**
     * Veritabanı varsayılanları kayıt anında henüz okunmaz; motor ve
     * active_flag hesabı bu değerlere kaydetmeden önce ihtiyaç duyduğu için
     * varsayılanlar modelde de tanımlı.
     */
    protected $attributes = [
        'cycle_length' => 28,
        'baby_count' => 1,
        'status' => self::STATUS_ACTIVE,
    ];

    protected function casts(): array
    {
        return [
            'input_date' => 'date',
            'lmp_date' => 'date',
            'due_date' => 'date',
            'cycle_length' => 'integer',
            'baby_count' => 'integer',
            'ended_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Türetilmiş alanlar tek yerden yazılır: girdi her değiştiğinde motor
        // yeniden çalışır, böylece lmp_date/due_date ile input_date ayrışamaz.
        static::saving(function (Pregnancy $pregnancy): void {
            $pregnancy->syncDerivedDates();
            $pregnancy->active_flag = $pregnancy->status === self::STATUS_ACTIVE ? 1 : null;
        });
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<PregnancyRedating, $this> */
    public function redatings(): HasMany
    {
        return $this->hasMany(PregnancyRedating::class)->orderBy('measured_on');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotNull('active_flag');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Etkin SAT ve TDT'yi girdiden (ve varsa USG düzeltmesinden) türetir.
     */
    public function syncDerivedDates(): void
    {
        $lmp = $this->relationLoaded('redatings') && $this->redatings->isNotEmpty()
            ? GestationalAge::applyRedating($this->redatingPayload())
            : GestationalAge::effectiveLmp($this->method, $this->input_date->toDateString(), $this->cycle_length);

        $this->lmp_date = $lmp;
        $this->due_date = GestationalAge::fromLmp($lmp, $lmp)['due_date'];
    }

    /**
     * Gebeliğin bugünkü durumu.
     *
     * "Bugün" kullanıcının kendi saat dilimindeki takvim günüdür — sunucu UTC'de
     * olduğu için bu dönüşüm atlanırsa kullanıcı günün bir kısmında yanlış
     * haftayı görür.
     *
     * @return array{
     *     lmp_date: string, due_date: string, ga_days: int, week: int, day: int,
     *     display: string, trimester: int, days_left: int, progress: float,
     *     is_overdue: bool, needs_completion_prompt: bool
     * }
     */
    public function gestationalAge(?string $today = null): array
    {
        return GestationalAge::fromLmp(
            $this->lmp_date->toDateString(),
            $today ?? $this->todayForUser(),
        );
    }

    public function todayForUser(): string
    {
        return Carbon::now($this->user->timezone ?? config('app.timezone'))->toDateString();
    }

    /**
     * Gebeliği kapatır.
     *
     * Kapanma anı bildirimler için tek gerçek kaynaktır: planlanmış işler
     * gönderimden hemen önce bu duruma bakar, böylece kuyruğa çoktan girmiş bir
     * haftalık bildirim kullanıcıya ulaşmaz.
     */
    public function end(?string $reason = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_ENDED,
            'ended_at' => now(),
            'ended_reason' => $reason,
            'active_flag' => null,
        ])->save();
    }

    /** @return list<array{measured_on: string, ga_days_at_measure: int}> */
    private function redatingPayload(): array
    {
        return $this->redatings
            ->map(fn (PregnancyRedating $r): array => [
                'measured_on' => $r->measured_on->toDateString(),
                'ga_days_at_measure' => $r->ga_days_at_measure,
            ])
            ->all();
    }
}
