<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePregnancyRequest;
use App\Http\Requests\StoreRedatingRequest;
use App\Http\Resources\PregnancyResource;
use App\Models\Pregnancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class PregnancyController extends Controller
{
    /** Aktif gebeliği ve bugünkü hafta durumunu döndürür. */
    public function current(Request $request): JsonResponse
    {
        $pregnancy = $request->user()->pregnancies()->active()->with('redatings')->first();

        // Aktif gebelik yokluğu bir hata değil, normal bir durum: yeni kullanıcı
        // henüz kurulumu yapmamıştır. 404 dönmek istemcide hata yolunu tetikler,
        // konsolu kirletir ve hata izleme araçlarında yanlış alarm üretir.
        return response()->json([
            'data' => $pregnancy === null ? null : new PregnancyResource($pregnancy),
        ]);
    }

    public function store(StorePregnancyRequest $request): JsonResponse
    {
        if ($request->user()->pregnancies()->active()->exists()) {
            return response()->json([
                'message' => 'Zaten aktif bir gebelik kaydınız var.',
                'code' => 'active_pregnancy_exists',
            ], 409);
        }

        try {
            $pregnancy = $request->user()->pregnancies()->create(
                $request->safe()->only(['method', 'input_date', 'cycle_length', 'baby_count']),
            );
        } catch (InvalidArgumentException $e) {
            return $this->engineError($e);
        }

        return response()->json(['data' => new PregnancyResource($pregnancy)], 201);
    }

    /** USG ile yeniden tarihleme; en son ölçüm geçerli olur. */
    public function redate(StoreRedatingRequest $request, Pregnancy $pregnancy): JsonResponse
    {
        $this->authorizeOwnership($request, $pregnancy);

        $pregnancy->redatings()->create($request->safe()->all());

        try {
            $pregnancy->load('redatings')->save();
        } catch (InvalidArgumentException $e) {
            return $this->engineError($e);
        }

        return response()->json(['data' => new PregnancyResource($pregnancy->load('redatings'))]);
    }

    /**
     * Gebeliği kapatır.
     *
     * Kapanan gebelik için planlanmış hiçbir bildirim gönderilmez; bunu
     * sağlayan kontrol gönderim anında Pregnancy::isActive() üzerinden yapılır.
     */
    public function end(Request $request, Pregnancy $pregnancy): JsonResponse
    {
        $this->authorizeOwnership($request, $pregnancy);

        $data = $request->validate([
            'reason' => ['sometimes', 'nullable', Rule::in(Pregnancy::REASONS)],
        ]);

        $pregnancy->end($data['reason'] ?? null);

        return response()->json(['data' => new PregnancyResource($pregnancy)]);
    }

    private function authorizeOwnership(Request $request, Pregnancy $pregnancy): void
    {
        abort_unless($pregnancy->user_id === $request->user()->id, 404);
    }

    private function engineError(InvalidArgumentException $e): JsonResponse
    {
        return response()->json([
            'message' => $e->getMessage(),
            'errors' => ['input_date' => [$e->getMessage()]],
        ], 422);
    }
}
