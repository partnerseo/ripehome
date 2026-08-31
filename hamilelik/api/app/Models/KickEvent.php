<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KickEvent extends Model
{
    public $timestamps = false;

    protected $fillable = ['occurred_at'];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    /** @return BelongsTo<KickSession, $this> */
    public function kickSession(): BelongsTo
    {
        return $this->belongsTo(KickSession::class);
    }
}
