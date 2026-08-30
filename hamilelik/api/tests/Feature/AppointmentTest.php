<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Pregnancy;
use App\Models\ScreeningTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function publishTemplates(): void
    {
        ScreeningTemplate::factory()->published()->create([
            'code' => 'nt_ikili', 'name' => 'NT ve ikili tarama',
            'category' => 'usg', 'week_start' => 11, 'week_end' => 14, 'sort' => 10,
        ]);
        ScreeningTemplate::factory()->published()->create([
            'code' => 'ogtt', 'name' => 'Şeker yükleme testi',
            'category' => 'lab', 'week_start' => 24, 'week_end' => 28, 'sort' => 20,
        ]);
    }

    #[Test]
    public function creating_a_pregnancy_plans_the_screening_windows(): void
    {
        Carbon::setTestNow('2026-06-22 09:00:00');
        $this->actingAsUser();
        $this->publishTemplates();

        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05'])
            ->assertCreated();

        $response = $this->getJson('/api/v1/appointments')->assertOk();

        $response->assertJsonCount(2, 'data');
        // SAT 5 Ocak: 11. hafta 23 Mart'ta başlar, 14. haftanın son günü 19 Nisan.
        $response->assertJsonPath('data.0.title', 'NT ve ikili tarama')
            ->assertJsonPath('data.0.window.start_on', '2026-03-23')
            ->assertJsonPath('data.0.window.end_on', '2026-04-19')
            ->assertJsonPath('data.0.source', 'auto');
    }

    #[Test]
    public function unreviewed_templates_never_become_appointments(): void
    {
        $this->actingAsUser();
        ScreeningTemplate::factory()->create(['code' => 'taslak_tetkik']);

        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05'])
            ->assertCreated();

        // Onaysız tetkik takvimi kullanıcıya randevu olarak da görünmez.
        $this->getJson('/api/v1/appointments')->assertOk()->assertJsonCount(0, 'data');
    }

    #[Test]
    public function redating_shifts_the_windows(): void
    {
        Carbon::setTestNow('2026-06-22 09:00:00');
        $this->actingAsUser();
        $this->publishTemplates();

        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05']);
        $pregnancy = Pregnancy::sole();

        $before = $pregnancy->appointments()->where('window_start_week', 24)->sole()->window_start_on;

        // Doktor "5 gün geridesiniz" dedi: pencereler de kaymalı.
        $this->postJson("/api/v1/pregnancies/{$pregnancy->id}/redate", [
            'measured_on' => '2026-04-10',
            'ga_days_at_measure' => 90,
        ])->assertOk();

        $after = $pregnancy->appointments()->where('window_start_week', 24)->sole()->window_start_on;

        $this->assertNotEquals($before->toDateString(), $after->toDateString());
        $this->assertSame(
            Pregnancy::sole()->lmp_date->copy()->addDays(24 * 7)->toDateString(),
            $after->toDateString(),
        );
    }

    #[Test]
    public function redating_does_not_move_an_appointment_the_user_already_booked(): void
    {
        Carbon::setTestNow('2026-06-22 09:00:00');
        $this->actingAsUser();
        $this->publishTemplates();

        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05']);
        $pregnancy = Pregnancy::sole();
        $appointment = $pregnancy->appointments()->where('window_start_week', 24)->sole();

        $this->patchJson("/api/v1/appointments/{$appointment->id}", [
            'scheduled_at' => '2026-07-01 10:30:00',
        ])->assertOk();

        $this->postJson("/api/v1/pregnancies/{$pregnancy->id}/redate", [
            'measured_on' => '2026-04-10',
            'ga_days_at_measure' => 90,
        ])->assertOk();

        // Hastaneden alınmış randevu, gebelik haftası düzeltildi diye kaymaz.
        $this->assertSame(
            '2026-07-01 10:30:00',
            $appointment->fresh()->scheduled_at->format('Y-m-d H:i:s'),
        );
    }

    #[Test]
    public function setting_a_date_moves_the_reminder_to_the_day_before(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05']);

        $this->postJson('/api/v1/appointments', [
            'title' => 'Kontrol muayenesi',
            'scheduled_at' => '2026-07-01 10:30:00',
        ])->assertCreated();

        $this->assertSame(
            '2026-06-30 10:30:00',
            Appointment::sole()->reminder_at->format('Y-m-d H:i:s'),
        );
    }

    #[Test]
    public function rescheduling_allows_the_reminder_to_fire_again(): void
    {
        $this->actingAsUser();
        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05']);
        $this->postJson('/api/v1/appointments', ['title' => 'Kontrol', 'scheduled_at' => '2026-07-01 10:30:00']);

        $appointment = Appointment::sole();
        $appointment->forceFill(['reminded_at' => now()])->save();

        $this->patchJson("/api/v1/appointments/{$appointment->id}", [
            'scheduled_at' => '2026-07-15 09:00:00',
        ])->assertOk();

        $this->assertNull($appointment->fresh()->reminded_at);
    }

    #[Test]
    public function deleting_an_auto_appointment_completes_it_instead(): void
    {
        $this->actingAsUser();
        $this->publishTemplates();
        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05']);

        $appointment = Appointment::where('source', Appointment::SOURCE_AUTO)->first();

        $this->deleteJson("/api/v1/appointments/{$appointment->id}")->assertOk();

        // Silinseydi bir sonraki planlama turunda yeniden üretilirdi.
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
        $this->assertNotNull($appointment->fresh()->completed_at);
    }

    #[Test]
    public function a_manual_appointment_is_really_deleted(): void
    {
        $this->actingAsUser();
        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => '2026-01-05']);
        $this->postJson('/api/v1/appointments', ['title' => 'Kontrol', 'scheduled_at' => '2026-07-01 10:30:00']);

        $this->deleteJson('/api/v1/appointments/'.Appointment::sole()->id)->assertNoContent();

        $this->assertDatabaseCount('appointments', 0);
    }

    #[Test]
    public function a_user_cannot_touch_another_users_appointment(): void
    {
        $other = User::factory()->create();
        $pregnancy = Pregnancy::factory()->for($other)->create();
        $appointment = Appointment::factory()->for($pregnancy)->create();

        $this->actingAsUser();

        $this->patchJson("/api/v1/appointments/{$appointment->id}", ['title' => 'Ele geçirildi'])->assertStatus(404);
        $this->deleteJson("/api/v1/appointments/{$appointment->id}")->assertStatus(404);
        $this->assertSame('Şeker yükleme testi', $appointment->fresh()->title);
    }

    #[Test]
    public function registering_a_device_is_idempotent(): void
    {
        $this->actingAsUser();

        $payload = ['expo_push_token' => 'ExponentPushToken[abc123]', 'platform' => 'ios'];

        $this->postJson('/api/v1/devices', $payload)->assertCreated();
        $this->postJson('/api/v1/devices', $payload)->assertCreated();

        $this->assertDatabaseCount('devices', 1);
    }

    #[Test]
    public function a_device_token_follows_the_user_who_last_signed_in(): void
    {
        $first = $this->actingAsUser();
        $this->postJson('/api/v1/devices', ['expo_push_token' => 'ExponentPushToken[shared]'])->assertCreated();

        // Aynı telefonda başka bir hesap açıldı: bildirimler eski hesaba gitmemeli.
        $second = $this->actingAsUser();
        $this->postJson('/api/v1/devices', ['expo_push_token' => 'ExponentPushToken[shared]'])->assertCreated();

        $this->assertDatabaseCount('devices', 1);
        $this->assertSame(0, $first->devices()->count());
        $this->assertSame(1, $second->devices()->count());
    }
}
