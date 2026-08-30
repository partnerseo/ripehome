<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScreeningTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScreeningTemplateController extends Controller
{
    /** Yayındaki tetkik takvimi. Sprint 4'te randevu üretiminin kaynağı olacak. */
    public function index(Request $request): JsonResponse
    {
        $templates = ScreeningTemplate::query()
            ->published()
            ->where('country', $request->query('country', 'TR'))
            ->where('locale', $request->user()?->locale ?? 'tr')
            ->orderBy('sort')
            ->orderBy('week_start')
            ->get()
            ->map(fn (ScreeningTemplate $t): array => [
                'code' => $t->code,
                'name' => $t->name,
                'description' => $t->description,
                'category' => $t->category,
                'week_start' => $t->week_start,
                'week_end' => $t->week_end,
                'is_optional' => $t->is_optional,
                'review' => [
                    'reviewed_by' => $t->reviewed_by,
                    'reviewed_at' => $t->reviewed_at?->toDateString(),
                ],
                'sources' => $t->source_refs ?? [],
            ]);

        return response()->json(['data' => $templates]);
    }
}
