<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberToken extends Model
{
    protected $fillable = [
        'member_id',
        'token_hash',
        'son_kullanma',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'son_kullanma' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function isExpired(): bool
    {
        return $this->son_kullanma->isPast();
    }
}
