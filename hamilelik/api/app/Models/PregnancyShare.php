<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PregnancyShareFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Eşin (veya bir yakının) gebeliğe salt-okunur erişimi.
 *
 * Paylaşım yalnızca okuma verir: davet edilen kişi kayıt ekleyemez,
 * düzenleyemez, gebeliği kapatamaz. Her an iptal edilebilir.
 */
class PregnancyShare extends Model
{
    /** @use HasFactory<PregnancyShareFactory> */
    use HasFactory;

    public const ROLE_VIEWER = 'viewer';

    protected $fillable = ['invited_email', 'user_id', 'role', 'token', 'accepted_at', 'revoked_at'];

    protected $attributes = ['role' => self::ROLE_VIEWER];

    protected function casts(): array
    {
        return ['accepted_at' => 'datetime', 'revoked_at' => 'datetime'];
    }

    public static function newToken(): string
    {
        return Str::random(48);
    }

    /** @return BelongsTo<Pregnancy, $this> */
    public function pregnancy(): BelongsTo
    {
        return $this->belongsTo(Pregnancy::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('revoked_at');
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null && $this->revoked_at === null;
    }
}
