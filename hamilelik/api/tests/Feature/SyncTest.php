<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Pregnancy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SyncTest extends TestCase
{
    use RefreshDatabase;

    private function actingWithPregnancy(): Pregnancy
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        return Pregnancy::factory()->for($user)->create([
            'method' => 'lmp',
            'input_date' => now()->subDays(200)->toDateString(),
        ]);
    }

    #[Test]
    public function sending_the_same_record_twice_creates_one_row(): void
    {
        $this->actingWithPregnancy();
        $uuid = (string) Str::uuid();

        $payload = ['health_logs' => [[
            'client_uuid' => $uuid,
            'type' => 'weight',
            'value_1' => 68.4,
            'measured_on' => '2026-06-22',
        ]]];

        // Bağlantı yanıtı almadan koparsa istemci aynı kuyruğu tekrar gönderir.
        $this->postJson('/api/v1/sync', $payload)->assertOk();
        $this->postJson('/api/v1/sync', $payload)->assertOk();

        $this->assertDatabaseCount('health_logs', 1);
    }

    #[Test]
    public function resending_a_kick_session_does_not_duplicate_its_events(): void
    {
        $this->actingWithPregnancy();
        $uuid = (string) Str::uuid();

        $payload = ['kick_sessions' => [[
            'client_uuid' => $uuid,
            'started_at' => '2026-06-22T09:00:00Z',
            'ended_at' => '2026-06-22T09:40:00Z',
            'events' => ['2026-06-22T09:05:00Z', '2026-06-22T09:12:00Z', '2026-06-22T09:20:00Z'],
        ]]];

        $this->postJson('/api/v1/sync', $payload)->assertOk();
        $this->postJson('/api/v1/sync', $payload)->assertOk();

        $this->assertDatabaseCount('kick_sessions', 1);
        $this->assertDatabaseCount('kick_events', 3);
    }

    #[Test]
    public function two_users_can_use_the_same_client_uuid(): void
    {
        // Anahtar cihazda üretiliyor; benzersizlik gebelik başına.
        $first = $this->actingWithPregnancy();
        $uuid = (string) Str::uuid();

        $payload = ['health_logs' => [[
            'client_uuid' => $uuid, 'type' => 'weight', 'value_1' => 68.4, 'measured_on' => '2026-06-22',
        ]]];

        $this->postJson('/api/v1/sync', $payload)->assertOk();

        $this->actingWithPregnancy();
        $this->postJson('/api/v1/sync', $payload)->assertOk();

        $this->assertDatabaseCount('health_logs', 2);
    }

    #[Test]
    public function a_batch_of_mixed_records_is_accepted(): void
    {
        $this->actingWithPregnancy();

        $response = $this->postJson('/api/v1/sync', [
            'health_logs' => [
                ['client_uuid' => (string) Str::uuid(), 'type' => 'weight', 'value_1' => 68.4, 'measured_on' => '2026-06-22'],
                ['client_uuid' => (string) Str::uuid(), 'type' => 'bp', 'value_1' => 118, 'value_2' => 76, 'measured_on' => '2026-06-22'],
            ],
            'symptom_logs' => [
                ['client_uuid' => (string) Str::uuid(), 'logged_on' => '2026-06-22', 'symptoms' => ['bulanti'], 'mood' => 4],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('accepted.health_logs', 2)
            ->assertJsonPath('accepted.symptom_logs', 1)
            ->assertJsonPath('alerts', []);
    }

    #[Test]
    public function a_high_blood_pressure_reading_returns_an_alert(): void
    {
        $this->actingWithPregnancy();

        $response = $this->postJson('/api/v1/sync', [
            'health_logs' => [[
                'client_uuid' => (string) Str::uuid(),
                'type' => 'bp', 'value_1' => 145, 'value_2' => 92, 'measured_on' => '2026-06-22',
            ]],
        ]);

        // Uygulama teşhis koymaz; başvurmayı söyler.
        $response->assertOk()
            ->assertJsonPath('alerts.0.type', 'blood_pressure')
            ->assertJsonCount(1, 'alerts');
    }

    #[Test]
    public function a_normal_blood_pressure_reading_does_not_alert(): void
    {
        $this->actingWithPregnancy();

        $this->postJson('/api/v1/sync', [
            'health_logs' => [[
                'client_uuid' => (string) Str::uuid(),
                'type' => 'bp', 'value_1' => 118, 'value_2' => 74, 'measured_on' => '2026-06-22',
            ]],
        ])->assertOk()->assertJsonPath('alerts', []);
    }

    #[Test]
    public function failing_to_count_ten_kicks_in_two_hours_alerts(): void
    {
        $this->actingWithPregnancy();

        $response = $this->postJson('/api/v1/sync', [
            'kick_sessions' => [[
                'client_uuid' => (string) Str::uuid(),
                'started_at' => '2026-06-22T09:00:00Z',
                'ended_at' => '2026-06-22T11:00:00Z',
                'events' => array_map(
                    fn (int $i): string => Carbon::parse('2026-06-22T09:00:00Z')->addMinutes($i * 10)->toIso8601String(),
                    range(1, 4),
                ),
            ]],
        ]);

        $response->assertOk()->assertJsonPath('alerts.0.type', 'fetal_movement');
    }

    #[Test]
    public function reaching_ten_kicks_quickly_does_not_alert(): void
    {
        $this->actingWithPregnancy();

        $this->postJson('/api/v1/sync', [
            'kick_sessions' => [[
                'client_uuid' => (string) Str::uuid(),
                'started_at' => '2026-06-22T09:00:00Z',
                'ended_at' => '2026-06-22T09:35:00Z',
                'events' => array_map(
                    fn (int $i): string => Carbon::parse('2026-06-22T09:00:00Z')->addMinutes($i * 3)->toIso8601String(),
                    range(1, 10),
                ),
            ]],
        ])->assertOk()->assertJsonPath('alerts', []);
    }

    #[Test]
    public function contractions_matching_five_one_one_alert(): void
    {
        $this->actingWithPregnancy();
        Carbon::setTestNow('2026-06-22 11:00:00');

        // Bir saat boyunca 5 dakikada bir gelen, 70 saniye süren kasılmalar.
        $start = Carbon::parse('2026-06-22 10:00:00');
        $contractions = [];

        for ($i = 0; $i <= 12; $i++) {
            $at = $start->copy()->addMinutes($i * 5);
            $contractions[] = [
                'started_at' => $at->toIso8601String(),
                'ended_at' => $at->copy()->addSeconds(70)->toIso8601String(),
                'duration_sec' => 70,
                'interval_sec' => $i === 0 ? null : 300,
            ];
        }

        $this->postJson('/api/v1/sync', [
            'contraction_sessions' => [[
                'client_uuid' => (string) Str::uuid(),
                'started_at' => $start->toIso8601String(),
                'contractions' => $contractions,
            ]],
        ])->assertOk()->assertJsonPath('alerts.0.type', 'contractions');
    }

    #[Test]
    public function occasional_contractions_do_not_alert(): void
    {
        $this->actingWithPregnancy();
        Carbon::setTestNow('2026-06-22 11:00:00');

        $start = Carbon::parse('2026-06-22 10:00:00');

        $this->postJson('/api/v1/sync', [
            'contraction_sessions' => [[
                'client_uuid' => (string) Str::uuid(),
                'started_at' => $start->toIso8601String(),
                'contractions' => [
                    ['started_at' => $start->toIso8601String(), 'ended_at' => $start->copy()->addSeconds(30)->toIso8601String(), 'duration_sec' => 30, 'interval_sec' => null],
                    ['started_at' => $start->copy()->addMinutes(25)->toIso8601String(), 'ended_at' => $start->copy()->addMinutes(25)->addSeconds(35)->toIso8601String(), 'duration_sec' => 35, 'interval_sec' => 1500],
                ],
            ]],
        ])->assertOk()->assertJsonPath('alerts', []);
    }

    #[Test]
    public function an_urgent_symptom_alerts(): void
    {
        $this->actingWithPregnancy();

        $this->postJson('/api/v1/sync', [
            'symptom_logs' => [[
                'client_uuid' => (string) Str::uuid(),
                'logged_on' => '2026-06-22',
                'symptoms' => ['bulanti', 'kanama'],
            ]],
        ])->assertOk()->assertJsonPath('alerts.0.type', 'symptom');
    }

    #[Test]
    public function sync_requires_an_active_pregnancy(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $this->postJson('/api/v1/sync', [
            'health_logs' => [['client_uuid' => (string) Str::uuid(), 'type' => 'weight', 'value_1' => 68, 'measured_on' => '2026-06-22']],
        ])->assertStatus(422)->assertJsonPath('code', 'no_active_pregnancy');
    }

    #[Test]
    public function sync_rejects_malformed_records(): void
    {
        $this->actingWithPregnancy();

        $this->postJson('/api/v1/sync', [
            'health_logs' => [['client_uuid' => 'uuid-degil', 'type' => 'kilo', 'value_1' => 'abc', 'measured_on' => '22.06.2026']],
        ])->assertStatus(422);

        $this->assertDatabaseCount('health_logs', 0);
    }

    #[Test]
    public function the_history_endpoints_return_what_was_synced(): void
    {
        $this->actingWithPregnancy();

        $this->postJson('/api/v1/sync', [
            'health_logs' => [
                ['client_uuid' => (string) Str::uuid(), 'type' => 'weight', 'value_1' => 68.4, 'measured_on' => '2026-06-20'],
                ['client_uuid' => (string) Str::uuid(), 'type' => 'bp', 'value_1' => 118, 'value_2' => 76, 'measured_on' => '2026-06-22'],
            ],
        ])->assertOk();

        $this->getJson('/api/v1/logs/health?type=weight')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.unit', 'kg');
    }

    #[Test]
    public function sync_requires_authentication(): void
    {
        $this->postJson('/api/v1/sync', [])->assertStatus(401);
        $this->getJson('/api/v1/logs/health')->assertStatus(401);
    }
}
