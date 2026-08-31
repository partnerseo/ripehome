<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContractionSession;
use App\Models\HealthLog;
use App\Models\KickSession;
use App\Models\Pregnancy;
use App\Models\SymptomLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Kayıt geçmişini okuma uçları. Yazma tek yoldan, /sync üzerinden yapılır. */
class TrackingController extends Controller
{
    public function health(Request $request): JsonResponse
    {
        $pregnancy = $this->pregnancy($request);

        if ($pregnancy === null) {
            return response()->json(['data' => []]);
        }

        $logs = $pregnancy->healthLogs()
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->query('from'), fn ($q, $from) => $q->where('measured_on', '>=', $from))
            ->when($request->query('to'), fn ($q, $to) => $q->where('measured_on', '<=', $to))
            ->orderBy('measured_on')
            ->get()
            ->map(fn (HealthLog $log): array => [
                'client_uuid' => $log->client_uuid,
                'type' => $log->type,
                'value_1' => $log->value_1,
                'value_2' => $log->value_2,
                'unit' => $log->unit,
                'measured_on' => $log->measured_on->toDateString(),
                'note' => $log->note,
                'needs_urgent_care' => $log->needsUrgentCare(),
            ]);

        return response()->json(['data' => $logs]);
    }

    public function kicks(Request $request): JsonResponse
    {
        $pregnancy = $this->pregnancy($request);

        if ($pregnancy === null) {
            return response()->json(['data' => []]);
        }

        $sessions = $pregnancy->kickSessions()
            ->latest('started_at')
            ->limit(50)
            ->get()
            ->map(fn (KickSession $s): array => [
                'client_uuid' => $s->client_uuid,
                'started_at' => $s->started_at->toIso8601String(),
                'ended_at' => $s->ended_at?->toIso8601String(),
                'kick_count' => $s->kick_count,
                'target_count' => $s->target_count,
                'duration_minutes' => $s->durationMinutes(),
                'reached_target' => $s->reachedTarget(),
                'needs_urgent_care' => $s->needsUrgentCare(),
            ]);

        return response()->json(['data' => $sessions]);
    }

    public function contractions(Request $request): JsonResponse
    {
        $pregnancy = $this->pregnancy($request);

        if ($pregnancy === null) {
            return response()->json(['data' => []]);
        }

        $sessions = $pregnancy->contractionSessions()
            ->with('contractions')
            ->latest('started_at')
            ->limit(20)
            ->get()
            ->map(fn (ContractionSession $s): array => [
                'client_uuid' => $s->client_uuid,
                'started_at' => $s->started_at->toIso8601String(),
                'ended_at' => $s->ended_at?->toIso8601String(),
                'count' => $s->contractions->count(),
                'meets_five_one_one' => $s->meetsFiveOneOne(),
            ]);

        return response()->json(['data' => $sessions]);
    }

    public function symptoms(Request $request): JsonResponse
    {
        $pregnancy = $this->pregnancy($request);

        if ($pregnancy === null) {
            return response()->json(['data' => []]);
        }

        $logs = $pregnancy->symptomLogs()
            ->latest('logged_on')
            ->limit(90)
            ->get()
            ->map(fn (SymptomLog $log): array => [
                'client_uuid' => $log->client_uuid,
                'logged_on' => $log->logged_on->toDateString(),
                'symptoms' => $log->symptoms ?? [],
                'mood' => $log->mood,
                'note' => $log->note,
                'urgent_symptoms' => $log->urgentSymptoms(),
            ]);

        return response()->json(['data' => $logs]);
    }

    private function pregnancy(Request $request): ?Pregnancy
    {
        return $request->user()->pregnancies()->active()->first();
    }
}
