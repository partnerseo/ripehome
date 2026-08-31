<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contraction extends Model
{
    public $timestamps = false;

    protected $fillable = ['started_at', 'ended_at', 'duration_sec', 'interval_sec'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'ended_at' => 'datetime'];
    }

    /** @return BelongsTo<ContractionSession, $this> */
    public function contractionSession(): BelongsTo
    {
        return $this->belongsTo(ContractionSession::class);
    }
}
