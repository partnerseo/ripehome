<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\HealthLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthLog extends Model
{
    /** @use HasFactory<HealthLogFactory> */
    use HasFactory;

    public const TYPES = ['weight', 'bp', 'glucose'];

    /** Türüne göre birim; istemci göndermez, sunucu belirler. */
    public const UNITS = ['weight' => 'kg', 'bp' => 'mmHg', 'glucose' => 'mg/dL'];

    /**
     * Bu değer ve üzeri tansiyon acil değerlendirme gerektirebilir.
     * Uygulama teşhis koymaz; yalnızca başvurmayı söyler.
     */
    public const BP_ALERT_SYSTOLIC = 140;

    public const BP_ALERT_DIASTOLIC = 90;

    protected $fillable = ['type', 'value_1', 'value_2', 'unit', 'measured_on', 'note', 'client_uuid'];

    protected function casts(): array
    {
        return [
            'value_1' => 'float',
            'value_2' => 'float',
            'measured_on' => 'date',
        ];
    }

    /** @return BelongsTo<Pregnancy, $this> */
    public function pregnancy(): BelongsTo
    {
        return $this->belongsTo(Pregnancy::class);
    }

    /** Kırmızı bayrak eşiği aşıldı mı? Arayüz buna göre yönlendirme gösterir. */
    public function needsUrgentCare(): bool
    {
        return $this->type === 'bp'
            && ($this->value_1 >= self::BP_ALERT_SYSTOLIC || ($this->value_2 ?? 0) >= self::BP_ALERT_DIASTOLIC);
    }
}
