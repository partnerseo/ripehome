<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Services\ChecklistTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChecklistController extends Controller
{
    public function __construct(private readonly ChecklistTemplate $template) {}

    /** Liste ilk açılışta hazır gelsin diye şablon burada kurulur. */
    public function index(Request $request): JsonResponse
    {
        $pregnancy = $request->user()->pregnancies()->active()->first();

        if ($pregnancy === null) {
            return response()->json(['data' => []]);
        }

        if ($pregnancy->checklistItems()->doesntExist()) {
            $this->template->seed($pregnancy);
        }

        $items = $pregnancy->checklistItems()
            ->orderBy('group')
            ->orderBy('sort')
            ->get()
            ->map(fn (ChecklistItem $i): array => [
                'id' => $i->id,
                'title' => $i->title,
                'group' => $i->group,
                'is_done' => $i->is_done,
            ]);

        return response()->json(['data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $pregnancy = $request->user()->pregnancies()->active()->first();

        if ($pregnancy === null) {
            return response()->json([
                'message' => 'Aktif bir gebelik kaydı gerekir.',
                'code' => 'no_active_pregnancy',
            ], 422);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'group' => ['sometimes', Rule::in(ChecklistItem::GROUPS)],
        ]);

        $item = $pregnancy->checklistItems()->create([
            ...$data,
            'sort' => (int) $pregnancy->checklistItems()->max('sort') + 10,
        ]);

        return response()->json(['data' => ['id' => $item->id, 'title' => $item->title]], 201);
    }

    public function update(Request $request, ChecklistItem $checklistItem): JsonResponse
    {
        abort_unless($checklistItem->pregnancy->user_id === $request->user()->id, 404);

        $data = $request->validate([
            'is_done' => ['sometimes', 'boolean'],
            'title' => ['sometimes', 'string', 'max:255'],
        ]);

        $checklistItem->update($data);

        return response()->json(['data' => ['id' => $checklistItem->id, 'is_done' => $checklistItem->is_done]]);
    }

    public function destroy(Request $request, ChecklistItem $checklistItem): JsonResponse
    {
        abort_unless($checklistItem->pregnancy->user_id === $request->user()->id, 404);

        $checklistItem->delete();

        return response()->json(null, 204);
    }
}
