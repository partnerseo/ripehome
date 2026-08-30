<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Appointment */
class AppointmentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category,
            'description' => $this->description,
            'is_optional' => $this->is_optional,
            'window' => [
                'start_week' => $this->window_start_week,
                'end_week' => $this->window_end_week,
                'start_on' => $this->window_start_on?->toDateString(),
                'end_on' => $this->window_end_on?->toDateString(),
            ],
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'location' => $this->location,
            'doctor_name' => $this->doctor_name,
            'notes' => $this->notes,
            'completed_at' => $this->completed_at?->toIso8601String(),
            'source' => $this->source,
        ];
    }
}
