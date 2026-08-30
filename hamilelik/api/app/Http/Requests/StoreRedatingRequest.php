<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRedatingRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'measured_on' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            // 44 haftaya kadar: ölçüm anındaki gebelik günü.
            'ga_days_at_measure' => ['required', 'integer', 'between:0,308'],
            'source' => ['sometimes', Rule::in(['usg', 'doctor'])],
            'note' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'measured_on.before_or_equal' => 'Ölçüm tarihi gelecekte olamaz.',
        ];
    }
}
