<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exceptions\MedicalReviewRequired;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ScreeningTemplateRequest;
use App\Models\ScreeningTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScreeningTemplateController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.screenings.index', [
            'templates' => ScreeningTemplate::query()
                ->when($request->query('durum'), fn ($q, $status) => $q->where('status', $status))
                ->orderBy('sort')
                ->orderBy('week_start')
                ->get(),
            'status' => $request->query('durum'),
        ]);
    }

    public function create(): View
    {
        return view('admin.screenings.form', ['template' => new ScreeningTemplate]);
    }

    public function store(ScreeningTemplateRequest $request): RedirectResponse
    {
        return $this->persist(new ScreeningTemplate, $request);
    }

    public function edit(ScreeningTemplate $screeningTemplate): View
    {
        return view('admin.screenings.form', ['template' => $screeningTemplate]);
    }

    public function update(ScreeningTemplateRequest $request, ScreeningTemplate $screeningTemplate): RedirectResponse
    {
        return $this->persist($screeningTemplate, $request);
    }

    private function persist(ScreeningTemplate $template, ScreeningTemplateRequest $request): RedirectResponse
    {
        $wasPublished = $template->exists && $template->isPublished();

        try {
            $template->fill($request->validated())->save();
        } catch (MedicalReviewRequired $e) {
            return back()->withInput()->withErrors(['status' => $e->getMessage()]);
        }

        $demoted = $wasPublished && ! $template->isPublished();

        return redirect()
            ->route('admin.screenings.edit', $template)
            ->with($demoted ? 'warn' : 'ok', $demoted
                ? 'Tetkik değiştiği için önceki onay geçersiz sayıldı; kayıt taslağa alındı.'
                : 'Kaydedildi.');
    }
}
