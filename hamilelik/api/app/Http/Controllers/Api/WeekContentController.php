<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WeekContentResource;
use App\Models\WeekContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WeekContentController extends Controller
{
    public function show(Request $request, int $week): JsonResponse
    {
        $content = WeekContent::query()
            ->published()
            ->where('week', $week)
            ->where('locale', $this->locale($request))
            ->first();

        if ($content === null) {
            return response()->json([
                'message' => 'Bu haftanın içeriği henüz yayında değil.',
                'code' => 'week_content_unavailable',
            ], 404);
        }

        return response()->json(['data' => new WeekContentResource($content)]);
    }

    /**
     * Tüm yayındaki haftalar — uygulama bunu bir kez indirip yerelde saklar.
     *
     * Hastanede internet olmadığı için içerik önceden inmiş olmalı. ETag ile
     * değişmediyse 304 döner; uygulama her açılışta 1–2 KB'lık bir kontrolle
     * güncelliğini doğrular, tüm paketi tekrar indirmez.
     */
    public function index(Request $request): JsonResponse|Response
    {
        $locale = $this->locale($request);

        $contents = WeekContent::query()
            ->published()
            ->where('locale', $locale)
            ->orderBy('week')
            ->get();

        $etag = $this->etagFor($locale, $contents->count(), $contents->max('updated_at')?->timestamp);

        if (trim($request->headers->get('If-None-Match', ''), '"') === $etag) {
            return response()->noContent(304)->setEtag($etag);
        }

        return response()
            ->json([
                'data' => WeekContentResource::collection($contents),
                'meta' => ['locale' => $locale, 'count' => $contents->count()],
            ])
            ->setEtag($etag);
    }

    private function locale(Request $request): string
    {
        $requested = $request->query('locale');

        return is_string($requested) && $requested !== ''
            ? $requested
            : ($request->user()?->locale ?? 'tr');
    }

    private function etagFor(string $locale, int $count, ?int $lastUpdate): string
    {
        return md5("{$locale}:{$count}:".($lastUpdate ?? 0));
    }
}
