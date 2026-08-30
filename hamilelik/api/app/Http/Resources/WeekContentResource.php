<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\WeekContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WeekContent */
class WeekContentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'week' => $this->week,
            'locale' => $this->locale,
            'baby_size_label' => $this->baby_size_label,
            'baby_length_mm' => $this->baby_length_mm,
            'baby_weight_g' => $this->baby_weight_g,
            'baby_body' => $this->baby_body,
            'mother_body' => $this->mother_body,
            'tips_body' => $this->tips_body,

            // Gözden geçirme bilgisi kullanıcıya gösterilir: içeriğin arkasında
            // kimin durduğunu görmek güvenin somut karşılığı.
            'review' => [
                'reviewed_by' => $this->reviewed_by,
                'reviewed_at' => $this->reviewed_at?->toDateString(),
            ],
            'sources' => $this->source_refs ?? [],
        ];
    }
}
