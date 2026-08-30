<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Pregnancy;
use App\Services\AppointmentPlanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentPlanner $planner) {}

    public function index(Request $request): JsonResponse
    {
        $pregnancy = $this->activePregnancy($request);

        if ($pregnancy === null) {
            return response()->json(['data' => []]);
        }

        $appointments = $pregnancy->appointments()
            ->orderByRaw('COALESCE(scheduled_at, window_start_on)')
            ->get();

        return response()->json(['data' => AppointmentResource::collection($appointments)]);
    }

    public function store(Request $request): JsonResponse
    {
        $pregnancy = $this->activePregnancy($request);

        if ($pregnancy === null) {
            return response()->json([
                'message' => 'Randevu eklemek için aktif bir gebelik kaydı gerekir.',
                'code' => 'no_active_pregnancy',
            ], 422);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['sometimes', Rule::in(['usg', 'lab', 'vaccine', 'visit'])],
            'scheduled_at' => ['required', 'date'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'doctor_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ]);

        $scheduledAt = Carbon::parse($data['scheduled_at']);

        $appointment = $pregnancy->appointments()->create([
            ...$data,
            'scheduled_at' => $scheduledAt,
            'reminder_at' => $this->planner->reminderForSchedule($scheduledAt),
            'source' => Appointment::SOURCE_MANUAL,
        ]);

        return response()->json(['data' => new AppointmentResource($appointment)], 201);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorizeOwnership($request, $appointment);

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'scheduled_at' => ['sometimes', 'nullable', 'date'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'doctor_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'completed_at' => ['sometimes', 'nullable', 'date'],
        ]);

        if (array_key_exists('scheduled_at', $data)) {
            $scheduledAt = $data['scheduled_at'] === null ? null : Carbon::parse($data['scheduled_at']);
            $data['scheduled_at'] = $scheduledAt;

            // Tarih değişti: hatırlatma da kaymalı, ve daha önce gönderilmiş
            // olsa bile yeni tarih için tekrar gönderilebilmeli.
            $data['reminder_at'] = $scheduledAt === null ? null : $this->planner->reminderForSchedule($scheduledAt);
            $appointment->reminded_at = null;
        }

        $appointment->fill($data)->save();

        return response()->json(['data' => new AppointmentResource($appointment)]);
    }

    public function destroy(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorizeOwnership($request, $appointment);

        // Otomatik üretilen tetkikler silinmez, tamamlandı işaretlenir: silinirse
        // bir sonraki planlama turunda yeniden üretilir ve kullanıcı aynı kaydı
        // tekrar tekrar siler.
        if ($appointment->source === Appointment::SOURCE_AUTO) {
            $appointment->forceFill(['completed_at' => now()])->save();

            return response()->json(['data' => new AppointmentResource($appointment)]);
        }

        $appointment->delete();

        return response()->json(null, 204);
    }

    private function activePregnancy(Request $request): ?Pregnancy
    {
        return $request->user()->pregnancies()->active()->first();
    }

    private function authorizeOwnership(Request $request, Appointment $appointment): void
    {
        abort_unless(
            $appointment->pregnancy->user_id === $request->user()->id,
            404,
        );
    }
}
