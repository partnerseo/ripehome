<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consent extends Model
{
    /**
     * Yürürlükteki rıza metni sürümü.
     *
     * Metin değiştiğinde bu sabit artırılır; eski sürüme rıza vermiş
     * kullanıcılardan yeniden rıza istenir. Hangi kullanıcının hangi metne
     * rıza verdiği böylece kayıtlı kalır.
     */
    public const CURRENT_VERSION = 'kvkk-2026-03';

    protected $fillable = ['version', 'accepted_at', 'withdrawn_at', 'ip_address'];

    protected function casts(): array
    {
        return ['accepted_at' => 'datetime', 'withdrawn_at' => 'datetime'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->withdrawn_at === null && $this->version === self::CURRENT_VERSION;
    }
}
