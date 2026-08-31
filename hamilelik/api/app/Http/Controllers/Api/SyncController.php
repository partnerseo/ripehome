<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncRequest;
use App\Services\SyncService;
use Illuminate\Http\JsonResponse;

class SyncController extends Controller
{
    public function __construct(private readonly SyncService $sync) {}

    public function __invoke(SyncRequest $request): JsonResponse
    {
        $pregnancy = $request->user()->pregnancies()->active()->first();

        if ($pregnancy === null) {
            return response()->json([
                'message' => 'Kayıt göndermek için aktif bir gebelik gerekir.',
                'code' => 'no_active_pregnancy',
            ], 422);
        }

        return response()->json($this->sync->apply($pregnancy, $request->validated()));
    }
}
