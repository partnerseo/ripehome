<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'ad',
        'soyad',
        'firma',
        'email',
        'ulke',
        'telefon',
        'durum',
        'banned_at',
    ];

    protected $casts = [
        'banned_at' => 'datetime',
    ];

    protected $hidden = []; // telefon sadece auth endpoint'lerinden döner

    public function tokens(): HasMany
    {
        return $this->hasMany(MemberToken::class);
    }

    public function securityLogs(): HasMany
    {
        return $this->hasMany(SecurityLog::class);
    }

    public function wholesaleOrders(): HasMany
    {
        return $this->hasMany(\App\Models\WholesaleOrder::class);
    }

    public function isActive(): bool
    {
        return $this->durum === 'onaylandi' && is_null($this->banned_at);
    }

    public function revokeAllTokens(): void
    {
        $this->tokens()->delete();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->ad} {$this->soyad}";
    }
}
