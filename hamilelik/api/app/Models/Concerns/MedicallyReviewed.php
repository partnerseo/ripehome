<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Exceptions\MedicalReviewRequired;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Tıbbi içeriğin yayına çıkma koşulları.
 *
 * Onay bir niyet değil, kaydın geçemeyeceği bir kapı olmalı: aksi hâlde acele
 * bir sürümde gözden geçirilmemiş bir tetkik haftası veya belirti metni
 * kullanıcıya gider. İki kural uygulanır:
 *
 * 1. Gözden geçiren kişi ve tarih olmadan durum "published" olamaz.
 * 2. Yayınlanmış bir kaydın tıbbi alanı değiştirilirse eski onay artık o metni
 *    kapsamaz; kayıt taslağa düşer ve yeniden gözden geçirilmesi gerekir.
 *
 * Kullanan model reviewableFields() ile hangi alanların tıbbi olduğunu bildirir.
 */
trait MedicallyReviewed
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_IN_REVIEW = 'in_review';

    public const STATUS_PUBLISHED = 'published';

    public static function bootMedicallyReviewed(): void
    {
        static::saving(function (Model $model): void {
            /** @var static $model */
            $model->demoteIfReviewedContentChanged();
            $model->guardPublishing();
        });
    }

    /** @return list<string> Onayın kapsadığı, değişince onayı geçersiz kılan alanlar. */
    abstract public function reviewableFields(): array;

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function hasReview(): bool
    {
        return filled($this->reviewed_by) && $this->reviewed_at !== null;
    }

    private function guardPublishing(): void
    {
        if ($this->status === self::STATUS_PUBLISHED && ! $this->hasReview()) {
            throw MedicalReviewRequired::forPublishing(class_basename($this));
        }
    }

    private function demoteIfReviewedContentChanged(): void
    {
        if (! $this->exists || $this->getOriginal('status') !== self::STATUS_PUBLISHED) {
            return;
        }

        if (! $this->isDirty($this->reviewableFields())) {
            return;
        }

        // Aynı kaydetmede yeni bir onay veriliyorsa metin zaten yeniden
        // gözden geçirilmiş demektir; kaydı taslağa düşürmeye gerek yok.
        if ($this->isDirty(['reviewed_by', 'reviewed_at'])) {
            return;
        }

        $this->status = self::STATUS_DRAFT;
        $this->reviewed_by = null;
        $this->reviewed_at = null;
        $this->review_note = 'Yayındaki metin değiştirildi; önceki onay geçersiz sayıldı.';
    }
}
