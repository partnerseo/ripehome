<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\ScreeningTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScreeningTemplateRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $id = $this->route('screeningTemplate')?->id;

        return [
            'code' => [
                'required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/',
                Rule::unique('screening_templates')->where(fn ($q) => $q
                    ->where('locale', $this->input('locale', 'tr'))
                    ->where('country', $this->input('country', 'TR')))->ignore($id),
            ],
            'locale' => ['required', Rule::in(['tr', 'en'])],
            'country' => ['required', Rule::in(['TR'])],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', Rule::in(ScreeningTemplate::CATEGORIES)],
            'week_start' => ['required', 'integer', 'between:1,42'],
            'week_end' => ['required', 'integer', 'between:1,42', 'gte:week_start'],
            'is_optional' => ['sometimes', 'boolean'],
            'sort' => ['nullable', 'integer', 'between:0,999'],

            'status' => ['required', Rule::in([
                ScreeningTemplate::STATUS_DRAFT, ScreeningTemplate::STATUS_IN_REVIEW, ScreeningTemplate::STATUS_PUBLISHED,
            ])],
            'reviewed_by' => ['nullable', 'required_if:status,'.ScreeningTemplate::STATUS_PUBLISHED, 'string', 'max:255'],
            'reviewed_at' => ['nullable', 'required_if:status,'.ScreeningTemplate::STATUS_PUBLISHED, 'date'],
            'review_note' => ['nullable', 'string', 'max:1000'],

            'source_refs' => ['nullable', 'array', 'max:10'],
            'source_refs.*.label' => ['nullable', 'string', 'max:255'],
            'source_refs.*.url' => ['nullable', 'url', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'code.regex' => 'Kod yalnızca küçük harf, rakam ve alt çizgi içerebilir.',
            'code.unique' => 'Bu kod bu ülke ve dil için zaten kullanılıyor.',
            'week_end.gte' => 'Bitiş haftası, başlangıç haftasından küçük olamaz.',
            'reviewed_by.required_if' => 'Yayına almak için gözden geçiren kişiyi yazın.',
            'reviewed_at.required_if' => 'Yayına almak için gözden geçirme tarihini girin.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_optional' => $this->boolean('is_optional')]);

        $sources = $this->input('source_refs');

        if (is_array($sources)) {
            $this->merge([
                'source_refs' => array_values(array_filter(
                    $sources,
                    fn ($row): bool => is_array($row) && filled($row['label'] ?? null),
                )),
            ]);
        }
    }
}
