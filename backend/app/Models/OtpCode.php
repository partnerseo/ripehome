<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = [
        'telefon',
        'kod_hash',
        'son_kullanma',
        'deneme_sayisi',
        'ip_adresi',
        'kullanildi_mi',
        'delivery_method',
        'email',
    ];

    protected $casts = [
        'son_kullanma'  => 'datetime',
        'kullanildi_mi' => 'boolean',
        'deneme_sayisi' => 'integer',
    ];

    public function isExpired(): bool
    {
        return $this->son_kullanma->isPast();
    }

    public function isUsed(): bool
    {
        return $this->kullanildi_mi;
    }

    public function markUsed(): void
    {
        $this->update(['kullanildi_mi' => true]);
    }
}
