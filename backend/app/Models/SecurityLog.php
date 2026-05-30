<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityLog extends Model
{
    protected $fillable = [
        'member_id',
        'olay',
        'ip',
        'user_agent',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    // Convenience: log olayları
    const OLAY_LOGIN       = 'login';
    const OLAY_FAILED_OTP  = 'failed_otp';
    const OLAY_OTP_LOCKED  = 'otp_locked';
    const OLAY_REVOKED     = 'token_revoked';
    const OLAY_BANNED      = 'banned';
    const OLAY_REGISTERED  = 'registered';
    const OLAY_LOGOUT      = 'logout';
}
