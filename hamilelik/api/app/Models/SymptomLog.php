<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SymptomLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SymptomLog extends Model
{
    /** @use HasFactory<SymptomLogFactory> */
    use HasFactory;

    /**
     * Acil değerlendirme gerektirebilecek belirtiler.
     * Seçildiklerinde uygulama teşhis koymaz, başvurmayı söyler.
     */
    public const URGENT = [
        'kanama',
        'siddetli_bas_agrisi',
        'gorme_bulaniklig',
        'sag_ust_karin_agrisi',
        'ani_odem',
        'su_gelmesi',
        'ates',
        'siddetli_karin_agrisi',
        'hareket_azalmasi',
    ];

    protected $fillable = ['logged_on', 'symptoms', 'mood', 'note', 'client_uuid'];

    protected function casts(): array
    {
        return ['logged_on' => 'date', 'symptoms' => 'array', 'mood' => 'integer'];
    }

    /** @return BelongsTo<Pregnancy, $this> */
    public function pregnancy(): BelongsTo
    {
        return $this->belongsTo(Pregnancy::class);
    }

    /** @return list<string> */
    public function urgentSymptoms(): array
    {
        return array_values(array_intersect($this->symptoms ?? [], self::URGENT));
    }

    public function needsUrgentCare(): bool
    {
        return $this->urgentSymptoms() !== [];
    }
}
