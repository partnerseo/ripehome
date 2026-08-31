<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\HealthLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncRequest extends FormRequest
{
    /** Tek gönderimde kabul edilen en fazla kayıt; kuyruk daha büyükse parçalanır. */
    public const MAX_PER_TYPE = 200;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'health_logs' => ['sometimes', 'array', 'max:'.self::MAX_PER_TYPE],
            'health_logs.*.client_uuid' => ['required', 'uuid'],
            'health_logs.*.type' => ['required', Rule::in(HealthLog::TYPES)],
            'health_logs.*.value_1' => ['required', 'numeric', 'between:0,999'],
            'health_logs.*.value_2' => ['nullable', 'numeric', 'between:0,999'],
            'health_logs.*.measured_on' => ['required', 'date_format:Y-m-d'],
            'health_logs.*.note' => ['nullable', 'string', 'max:1000'],

            'kick_sessions' => ['sometimes', 'array', 'max:'.self::MAX_PER_TYPE],
            'kick_sessions.*.client_uuid' => ['required', 'uuid'],
            'kick_sessions.*.started_at' => ['required', 'date'],
            'kick_sessions.*.ended_at' => ['nullable', 'date', 'after_or_equal:kick_sessions.*.started_at'],
            'kick_sessions.*.target_count' => ['sometimes', 'integer', 'between:1,50'],
            'kick_sessions.*.events' => ['sometimes', 'array', 'max:100'],
            'kick_sessions.*.events.*' => ['required', 'date'],

            'contraction_sessions' => ['sometimes', 'array', 'max:'.self::MAX_PER_TYPE],
            'contraction_sessions.*.client_uuid' => ['required', 'uuid'],
            'contraction_sessions.*.started_at' => ['required', 'date'],
            'contraction_sessions.*.ended_at' => ['nullable', 'date'],
            'contraction_sessions.*.contractions' => ['sometimes', 'array', 'max:200'],
            'contraction_sessions.*.contractions.*.started_at' => ['required', 'date'],
            'contraction_sessions.*.contractions.*.ended_at' => ['required', 'date'],
            'contraction_sessions.*.contractions.*.duration_sec' => ['required', 'integer', 'between:1,3600'],
            'contraction_sessions.*.contractions.*.interval_sec' => ['nullable', 'integer', 'between:0,7200'],

            'symptom_logs' => ['sometimes', 'array', 'max:'.self::MAX_PER_TYPE],
            'symptom_logs.*.client_uuid' => ['required', 'uuid'],
            'symptom_logs.*.logged_on' => ['required', 'date_format:Y-m-d'],
            'symptom_logs.*.symptoms' => ['sometimes', 'array', 'max:30'],
            'symptom_logs.*.symptoms.*' => ['string', 'max:64'],
            'symptom_logs.*.mood' => ['nullable', 'integer', 'between:1,5'],
            'symptom_logs.*.note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
