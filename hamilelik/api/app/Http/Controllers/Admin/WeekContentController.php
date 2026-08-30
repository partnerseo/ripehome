<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exceptions\MedicalReviewRequired;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WeekContentRequest;
use App\Models\WeekContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeekContentController extends Controller
{
    public function index(Request $request): View
    {
        $contents = WeekContent::query()
            ->when($request->query('durum'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('dil'), fn ($q, $locale) => $q->where('locale', $locale))
            ->orderBy('week')
            ->paginate(25)
            ->withQueryString();

        return view('admin.weeks.index', [
            'contents' => $contents,
            'status' => $request->query('durum'),
            'locale' => $request->query('dil'),
        ]);
    }

    public function create(): View
    {
        return view('admin.weeks.form', ['content' => new WeekContent]);
    }

    public function store(WeekContentRequest $request): RedirectResponse
    {
        return $this->persist(new WeekContent, $request);
    }

    public function edit(WeekContent $weekContent): View
    {
        return view('admin.weeks.form', ['content' => $weekContent]);
    }

    public function update(WeekContentRequest $request, WeekContent $weekContent): RedirectResponse
    {
        return $this->persist($weekContent, $request);
    }

    public function destroy(WeekContent $weekContent): RedirectResponse
    {
        $weekContent->delete();

        return redirect()->route('admin.weeks.index')->with('ok', 'Hafta içeriği silindi.');
    }

    private function persist(WeekContent $content, WeekContentRequest $request): RedirectResponse
    {
        $wasPublished = $content->exists && $content->isPublished();

        try {
            $content->fill($request->validated())->save();
        } catch (MedicalReviewRequired $e) {
            return back()->withInput()->withErrors(['status' => $e->getMessage()]);
        }

        // Model, yayındaki bir metin değiştirildiğinde onayı geçersiz sayıp
        // kaydı taslağa düşürür. Editör bunu fark etmeli.
        $demoted = $wasPublished && ! $content->isPublished();

        return redirect()
            ->route('admin.weeks.edit', $content)
            ->with($demoted ? 'warn' : 'ok', $demoted
                ? 'Metin değiştiği için önceki onay geçersiz sayıldı; kayıt taslağa alındı.'
                : 'Kaydedildi.');
    }
}
