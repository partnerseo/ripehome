<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpRateLimit extends Model
{
    protected $fillable = [
        'anahtar',
        'tip',
        'istek_sayisi',
        'pencere_baslangici',
    ];

    protected $casts = [
        'pencere_baslangici' => 'datetime',
        'istek_sayisi'       => 'integer',
    ];
}
