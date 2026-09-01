<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BellyPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BellyPhotoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pregnancy = $request->user()->pregnancies()->active()->first();

        if ($pregnancy === null) {
            return response()->json(['data' => []]);
        }

        $photos = $pregnancy->bellyPhotos()->orderBy('week')->get()->map(fn (BellyPhoto $p): array => [
            'id' => $p->id,
            'week' => $p->week,
            'taken_on' => $p->taken_on->toDateString(),
        ]);

        return response()->json(['data' => $photos]);
    }

    public function store(Request $request): JsonResponse
    {
        $pregnancy = $request->user()->pregnancies()->active()->first();

        if ($pregnancy === null) {
            return response()->json([
                'message' => 'Fotoğraf eklemek için aktif bir gebelik kaydı gerekir.',
                'code' => 'no_active_pregnancy',
            ], 422);
        }

        $data = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,heic', 'max:12288'],
            'week' => ['required', 'integer', 'between:1,42'],
            'taken_on' => ['sometimes', 'date_format:Y-m-d'],
        ]);

        // Herkese açık diskte değil: sağlık verisi, yalnızca yetkili istekle servis edilir.
        $path = $request->file('photo')->store("belly/{$pregnancy->id}", BellyPhoto::DISK);

        $photo = $pregnancy->bellyPhotos()->create([
            'week' => $data['week'],
            'path' => $path,
            'taken_on' => $data['taken_on'] ?? now()->toDateString(),
        ]);

        return response()->json(['data' => ['id' => $photo->id, 'week' => $photo->week]], 201);
    }

    /** Dosyayı yalnızca sahibine servis eder; herkese açık URL yok. */
    public function show(Request $request, BellyPhoto $bellyPhoto): StreamedResponse
    {
        abort_unless($bellyPhoto->pregnancy->user_id === $request->user()->id, 404);
        abort_unless(Storage::disk(BellyPhoto::DISK)->exists($bellyPhoto->path), 404);

        return Storage::disk(BellyPhoto::DISK)->response($bellyPhoto->path);
    }

    public function destroy(Request $request, BellyPhoto $bellyPhoto): JsonResponse
    {
        abort_unless($bellyPhoto->pregnancy->user_id === $request->user()->id, 404);

        $bellyPhoto->delete();

        return response()->json(null, 204);
    }
}
