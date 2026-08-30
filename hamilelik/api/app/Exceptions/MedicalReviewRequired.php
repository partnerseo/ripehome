<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class MedicalReviewRequired extends RuntimeException
{
    public static function forPublishing(string $model): self
    {
        return new self(
            "{$model} kaydı tıbbi gözden geçirme olmadan yayına alınamaz: ".
            'gözden geçiren kişi ve tarih zorunlu.',
        );
    }
}
