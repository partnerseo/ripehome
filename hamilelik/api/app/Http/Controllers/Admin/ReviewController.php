<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreeningTemplate;
use App\Models\WeekContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Hekimin gözden geçirme akışı.
 *
 * Editörün düzenleme formundan ayrı: hekim metin yazmaz, okur ve onaylar.
 * Formda 15 alan arasında dolaşmak yerine okunacak metni ve dayanak
 * kaynakları görür, altında tek bir onay kutusu vardır.
 */
class ReviewController extends Controller
{
    private const CATEGORY_LABELS = [
        'usg' => 'Ultrason',
        'lab' => 'Laboratuvar',
        'vaccine' => 'Aşı / immünglobulin',
        'visit' => 'Muayene',
    ];

    public function index(): View
    {
        return view('admin.review.index', [
            'weeks' => WeekContent::query()
                ->whereIn('status', [WeekContent::STATUS_IN_REVIEW, WeekContent::STATUS_DRAFT])
                ->orderByRaw("status = '".WeekContent::STATUS_IN_REVIEW."' desc")
                ->orderBy('week')
                ->get(),
            'screenings' => ScreeningTemplate::query()
                ->whereIn('status', [ScreeningTemplate::STATUS_IN_REVIEW, ScreeningTemplate::STATUS_DRAFT])
                ->orderBy('sort')
                ->get(),
        ]);
    }

    public function show(string $type, int $id): View
    {
        $record = $this->find($type, $id);

        return view('admin.review.show', [
            'type' => $type,
            'record' => $record,
            'sections' => $this->sections($record),
        ]);
    }

    public function approve(Request $request, string $type, int $id): RedirectResponse
    {
        $record = $this->find($type, $id);

        $data = $request->validate([
            'reviewed_by' => ['required', 'string', 'max:255'],
            'reviewed_at' => ['required', 'date'],
            'review_note' => ['nullable', 'string', 'max:1000'],
        ], [
            'reviewed_by.required' => 'Onaylayan kişiyi yazın.',
            'reviewed_at.required' => 'Onay tarihini girin.',
        ]);

        $record->fill([...$data, 'status' => $record::STATUS_PUBLISHED])->save();

        return redirect()
            ->route('admin.review.index')
            ->with('ok', $this->title($record).' onaylandı ve yayına alındı.');
    }

    /** @return WeekContent|ScreeningTemplate */
    private function find(string $type, int $id): Model
    {
        return $type === 'hafta'
            ? WeekContent::findOrFail($id)
            : ScreeningTemplate::findOrFail($id);
    }

    /**
     * Hekimin okuyacağı bölümler — onayın kapsadığı alanlarla aynı küme.
     *
     * @return array<string, string|null>
     */
    private function sections(Model $record): array
    {
        if ($record instanceof WeekContent) {
            return [
                'Boyut karşılaştırması' => $record->baby_size_label,
                'Ölçüler' => $this->measurements($record),
                'Bebekte neler oluyor' => $record->baby_body,
                'Annede neler oluyor' => $record->mother_body,
                'Bu hafta ipuçları' => $record->tips_body,
            ];
        }

        /** @var ScreeningTemplate $record */
        return [
            'Kategori' => self::CATEGORY_LABELS[$record->category] ?? $record->category,
            'Zaman aralığı' => $record->week_start === $record->week_end
                ? "{$record->week_start}. hafta"
                : "{$record->week_start}–{$record->week_end}. hafta",
            'Tercihe bağlı' => $record->is_optional ? 'Evet' : 'Hayır',
            'Açıklama' => $record->description,
        ];
    }

    private function measurements(WeekContent $record): ?string
    {
        $parts = array_filter([
            $record->baby_length_mm !== null ? number_format($record->baby_length_mm / 10, 1).' cm' : null,
            $record->baby_weight_g !== null ? "{$record->baby_weight_g} g" : null,
        ]);

        return $parts === [] ? null : implode(' · ', $parts);
    }

    private function title(Model $record): string
    {
        return $record instanceof WeekContent ? "{$record->week}. hafta" : $record->name;
    }
}
