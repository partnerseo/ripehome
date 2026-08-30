<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Pregnancy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Pregnancy */
class PregnancyResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'method' => $this->method,
            'input_date' => $this->input_date->toDateString(),
            'cycle_length' => $this->cycle_length,
            'baby_count' => $this->baby_count,
            'status' => $this->status,
            'ended_at' => $this->ended_at?->toIso8601String(),
            'ended_reason' => $this->ended_reason,

            // Kapanmış gebelikte hafta ve geri sayım gösterilmez: kaybettiği
            // gebeliğin haftasını görmek kullanıcının uygulamayı sildiği andır.
            'gestational_age' => $this->when($this->isActive(), fn () => $this->gestationalAge()),

            'redatings' => $this->whenLoaded('redatings', fn () => $this->redatings->map(fn ($r) => [
                'id' => $r->id,
                'measured_on' => $r->measured_on->toDateString(),
                'ga_days_at_measure' => $r->ga_days_at_measure,
                'source' => $r->source,
                'note' => $r->note,
            ])),
        ];
    }
}
