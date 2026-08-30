<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\WeekContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WeekContentRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $id = $this->route('weekContent')?->id;

        return [
            'week' => [
                'required', 'integer', 'between:1,42',
                Rule::unique('week_contents')->where(
                    fn ($q) => $q->where('locale', $this->input('locale', 'tr')),
                )->ignore($id),
            ],
            'locale' => ['required', Rule::in(['tr', 'en'])],
            'baby_size_label' => ['nullable', 'string', 'max:255'],
            'baby_length_mm' => ['nullable', 'integer', 'between:0,600'],
            'baby_weight_g' => ['nullable', 'integer', 'between:0,6000'],
            'baby_body' => ['nullable', 'string', 'max:4000'],
            'mother_body' => ['nullable', 'string', 'max:4000'],
            'tips_body' => ['nullable', 'string', 'max:4000'],

            'status' => ['required', Rule::in([
                WeekContent::STATUS_DRAFT, WeekContent::STATUS_IN_REVIEW, WeekContent::STATUS_PUBLISHED,
            ])],
            // Yayına almanın ön koşulu modelde de zorlanır; buradaki kural
            // kullanıcıya hata sayfası yerine alanın yanında mesaj gösterir.
            'reviewed_by' => ['nullable', 'required_if:status,'.WeekContent::STATUS_PUBLISHED, 'string', 'max:255'],
            'reviewed_at' => ['nullable', 'required_if:status,'.WeekContent::STATUS_PUBLISHED, 'date'],
            'review_note' => ['nullable', 'string', 'max:1000'],

            'source_refs' => ['nullable', 'array', 'max:10'],
            'source_refs.*.label' => ['required_with:source_refs.*.url', 'nullable', 'string', 'max:255'],
            'source_refs.*.url' => ['nullable', 'url', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'week.unique' => 'Bu dilde bu hafta için zaten bir içerik var.',
            'reviewed_by.required_if' => 'Yayına almak için gözden geçiren kişiyi yazın.',
            'reviewed_at.required_if' => 'Yayına almak için gözden geçirme tarihini girin.',
        ];
    }

    /** Boş bırakılmış kaynak satırlarını temizler. */
    protected function prepareForValidation(): void
    {
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
