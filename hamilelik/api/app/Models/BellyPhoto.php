<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BellyPhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BellyPhoto extends Model
{
    /** @use HasFactory<BellyPhotoFactory> */
    use HasFactory;

    /** Fotoğraflar herkese açık diskte değil: sağlık verisi. */
    public const DISK = 'local';

    protected $fillable = ['week', 'path', 'taken_on'];

    protected function casts(): array
    {
        return ['week' => 'integer', 'taken_on' => 'date'];
    }

    /** @return BelongsTo<Pregnancy, $this> */
    public function pregnancy(): BelongsTo
    {
        return $this->belongsTo(Pregnancy::class);
    }

    protected static function booted(): void
    {
        // Kayıt silinince dosya da gitsin; yetim dosya birikmesin.
        static::deleted(function (BellyPhoto $photo): void {
            Storage::disk(self::DISK)->delete($photo->path);
        });
    }
}
