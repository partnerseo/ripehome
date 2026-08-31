<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HealthLog;
use App\Models\KickSession;
use App\Models\Pregnancy;
use Illuminate\Support\Facades\DB;

/**
 * Çevrimdışı yazılan kayıtların toplu gönderimi.
 *
 * Uygulama her kaydı önce cihaza yazar, sonra kuyruğu buraya gönderir.
 * Her kayıt cihazda üretilmiş bir client_uuid taşır; aynı anahtar iki kez
 * gelirse ikinci gönderim kopya üretmez. Bağlantı yanıtı almadan koptuğunda
 * istemci güvenle tekrar deneyebilir — hastanede sık olan tam da bu.
 */
class SyncService
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{accepted: array<string, int>, alerts: list<array{type: string, reference: string, detail: string}>}
     */
    public function apply(Pregnancy $pregnancy, array $payload): array
    {
        $accepted = ['health_logs' => 0, 'kick_sessions' => 0, 'contraction_sessions' => 0, 'symptom_logs' => 0];
        $alerts = [];

        DB::transaction(function () use ($pregnancy, $payload, &$accepted, &$alerts): void {
            foreach ($payload['health_logs'] ?? [] as $row) {
                $log = $this->upsert($pregnancy->healthLogs(), $row['client_uuid'], [
                    'type' => $row['type'],
                    'value_1' => $row['value_1'],
                    'value_2' => $row['value_2'] ?? null,
                    'unit' => HealthLog::UNITS[$row['type']],
                    'measured_on' => $row['measured_on'],
                    'note' => $row['note'] ?? null,
                ]);
                $accepted['health_logs']++;

                if ($log->needsUrgentCare()) {
                    $alerts[] = $this->alert('blood_pressure', $row['client_uuid'],
                        'Girilen tansiyon değeri acil değerlendirme gerektirebilir.');
                }
            }

            foreach ($payload['kick_sessions'] ?? [] as $row) {
                $session = $this->upsert($pregnancy->kickSessions(), $row['client_uuid'], [
                    'started_at' => $row['started_at'],
                    'ended_at' => $row['ended_at'] ?? null,
                    'target_count' => $row['target_count'] ?? KickSession::TARGET,
                    'kick_count' => count($row['events'] ?? []),
                ]);

                // Olaylar oturumla birlikte yeniden yazılır: tekrar gönderim
                // aynı hareketleri iki kez eklememeli.
                $session->events()->delete();
                $session->events()->createMany(
                    array_map(fn (string $at): array => ['occurred_at' => $at], $row['events'] ?? []),
                );
                $accepted['kick_sessions']++;

                if ($session->needsUrgentCare()) {
                    $alerts[] = $this->alert('fetal_movement', $row['client_uuid'],
                        'İki saatte 10 hareket sayılamadı. Doktorunuza başvurun.');
                }
            }

            foreach ($payload['contraction_sessions'] ?? [] as $row) {
                $session = $this->upsert($pregnancy->contractionSessions(), $row['client_uuid'], [
                    'started_at' => $row['started_at'],
                    'ended_at' => $row['ended_at'] ?? null,
                ]);

                $session->contractions()->delete();
                $session->contractions()->createMany($row['contractions'] ?? []);
                $accepted['contraction_sessions']++;

                if ($session->load('contractions')->meetsFiveOneOne()) {
                    $alerts[] = $this->alert('contractions', $row['client_uuid'],
                        'Kasılmalar 5-1-1 kuralına uyuyor. Hastaneye başvurma zamanı.');
                }
            }

            foreach ($payload['symptom_logs'] ?? [] as $row) {
                $log = $this->upsert($pregnancy->symptomLogs(), $row['client_uuid'], [
                    'logged_on' => $row['logged_on'],
                    'symptoms' => $row['symptoms'] ?? [],
                    'mood' => $row['mood'] ?? null,
                    'note' => $row['note'] ?? null,
                ]);
                $accepted['symptom_logs']++;

                if ($log->needsUrgentCare()) {
                    $alerts[] = $this->alert('symptom', $row['client_uuid'],
                        'İşaretlediğiniz belirti acil değerlendirme gerektirebilir.');
                }
            }
        });

        return ['accepted' => $accepted, 'alerts' => $alerts];
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     */
    private function upsert(mixed $relation, string $clientUuid, array $attributes): mixed
    {
        return $relation->updateOrCreate(['client_uuid' => $clientUuid], $attributes);
    }

    /** @return array{type: string, reference: string, detail: string} */
    private function alert(string $type, string $reference, string $detail): array
    {
        return ['type' => $type, 'reference' => $reference, 'detail' => $detail];
    }
}
