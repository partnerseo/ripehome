<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Pregnancy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PregnancyTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    #[Test]
    public function it_creates_a_pregnancy_and_returns_the_current_week(): void
    {
        Carbon::setTestNow('2026-06-22 09:00:00');
        $this->actingAsUser();

        $response = $this->postJson('/api/v1/pregnancies', [
            'method' => 'lmp',
            'input_date' => '2026-01-05',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.gestational_age.display', '24h 0g')
            ->assertJsonPath('data.gestational_age.due_date', '2026-10-12')
            ->assertJsonPath('data.gestational_age.trimester', 2);
    }

    #[Test]
    public function the_due_date_is_persisted_not_recomputed_on_read(): void
    {
        Carbon::setTestNow('2026-06-22 09:00:00');
        $this->actingAsUser();

        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05']);

        $stored = Pregnancy::sole();

        $this->assertSame('2026-01-05', $stored->lmp_date->toDateString());
        $this->assertSame('2026-10-12', $stored->due_date->toDateString());
    }

    #[Test]
    public function ivf_and_due_date_inputs_land_on_the_same_week(): void
    {
        Carbon::setTestNow('2026-06-22 09:00:00');

        foreach ([
            ['method' => 'lmp', 'input_date' => '2026-01-05'],
            ['method' => 'due_date', 'input_date' => '2026-10-12'],
            ['method' => 'conception', 'input_date' => '2026-01-19'],
            ['method' => 'ivf_d5', 'input_date' => '2026-01-24'],
            ['method' => 'ivf_d3', 'input_date' => '2026-01-22'],
        ] as $payload) {
            $this->actingAsUser();

            $this->postJson('/api/v1/pregnancies', $payload)
                ->assertCreated()
                ->assertJsonPath('data.gestational_age.display', '24h 0g');
        }
    }

    #[Test]
    public function a_user_can_only_have_one_active_pregnancy(): void
    {
        $this->actingAsUser();
        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05'])->assertCreated();

        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-02-05'])
            ->assertStatus(409)
            ->assertJsonPath('code', 'active_pregnancy_exists');
    }

    #[Test]
    public function a_closed_pregnancy_frees_the_slot(): void
    {
        $user = $this->actingAsUser();
        $pregnancy = Pregnancy::factory()->for($user)->create();

        $this->postJson("/api/v1/pregnancies/{$pregnancy->id}/end", ['reason' => 'birth'])->assertOk();

        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05'])
            ->assertCreated();
    }

    #[Test]
    public function current_returns_404_when_there_is_no_active_pregnancy(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/v1/pregnancies/current')
            ->assertStatus(404)
            ->assertJsonPath('code', 'no_active_pregnancy');
    }

    #[Test]
    public function a_closed_pregnancy_hides_the_week_and_countdown(): void
    {
        $user = $this->actingAsUser();
        $pregnancy = Pregnancy::factory()->for($user)->create();

        $response = $this->postJson("/api/v1/pregnancies/{$pregnancy->id}/end", ['reason' => 'loss']);

        // Kaybedilen gebelikte hafta ve geri sayım dönmemeli.
        $response->assertOk()
            ->assertJsonPath('data.status', 'ended')
            ->assertJsonPath('data.ended_reason', 'loss')
            ->assertJsonMissingPath('data.gestational_age');

        $this->getJson('/api/v1/pregnancies/current')->assertStatus(404);
    }

    #[Test]
    public function redating_shifts_the_week(): void
    {
        Carbon::setTestNow('2026-06-22 09:00:00');
        $user = $this->actingAsUser();
        $pregnancy = Pregnancy::factory()->for($user)->create([
            'method' => 'lmp',
            'input_date' => '2026-01-05',
        ]);

        $this->postJson("/api/v1/pregnancies/{$pregnancy->id}/redate", [
            'measured_on' => '2026-04-10',
            'ga_days_at_measure' => 100,
        ])->assertOk()
            ->assertJsonPath('data.gestational_age.ga_days', 173)
            ->assertJsonPath('data.gestational_age.display', '24h 5g');

        $this->assertSame('2025-12-31', $pregnancy->fresh()->lmp_date->toDateString());
    }

    #[Test]
    public function the_week_follows_the_users_timezone_not_the_server(): void
    {
        // Sunucuda UTC'de 21 Haziran 23:30; Istanbul'da (UTC+3) çoktan 22 Haziran.
        Carbon::setTestNow('2026-06-21 23:30:00');

        $istanbul = $this->actingAsUser(['timezone' => 'Europe/Istanbul']);
        $pregnancy = Pregnancy::factory()->for($istanbul)->create([
            'method' => 'lmp',
            'input_date' => '2026-01-05',
        ]);

        $this->getJson('/api/v1/pregnancies/current')
            ->assertOk()
            ->assertJsonPath('data.gestational_age.display', '24h 0g');

        // Aynı anda Los Angeles'ta (UTC-7) hâlâ 21 Haziran: bir gün geride.
        $la = $this->actingAsUser(['timezone' => 'America/Los_Angeles']);
        Pregnancy::factory()->for($la)->create([
            'method' => 'lmp',
            'input_date' => '2026-01-05',
        ]);

        $this->getJson('/api/v1/pregnancies/current')
            ->assertOk()
            ->assertJsonPath('data.gestational_age.display', '23h 6g');
    }

    #[Test]
    public function a_user_cannot_touch_another_users_pregnancy(): void
    {
        $other = User::factory()->create();
        $pregnancy = Pregnancy::factory()->for($other)->create();

        $this->actingAsUser();

        $this->postJson("/api/v1/pregnancies/{$pregnancy->id}/end", ['reason' => 'birth'])->assertStatus(404);
        $this->postJson("/api/v1/pregnancies/{$pregnancy->id}/redate", [
            'measured_on' => '2026-04-10',
            'ga_days_at_measure' => 100,
        ])->assertStatus(404);

        $this->assertDatabaseHas('pregnancies', ['id' => $pregnancy->id, 'status' => 'active']);
    }

    #[Test]
    public function it_rejects_a_future_last_period_date(): void
    {
        Carbon::setTestNow('2026-06-22 09:00:00');
        $this->actingAsUser();

        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-06-23'])
            ->assertStatus(422);

        $this->assertDatabaseCount('pregnancies', 0);
    }

    #[Test]
    public function it_rejects_invalid_input(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/v1/pregnancies', ['method' => 'gebelik', 'input_date' => '2026-01-05'])->assertStatus(422);
        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '05.01.2026'])->assertStatus(422);
        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05', 'cycle_length' => 60])->assertStatus(422);
    }
}
