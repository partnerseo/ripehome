<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PregnancyRedating extends Model
{
    protected $fillable = ['measured_on', 'ga_days_at_measure', 'source', 'note'];

    protected function casts(): array
    {
        return [
            'measured_on' => 'date',
            'ga_days_at_measure' => 'integer',
        ];
    }

    /** @return BelongsTo<Pregnancy, $this> */
    public function pregnancy(): BelongsTo
    {
        return $this->belongsTo(Pregnancy::class);
    }
}
