<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendAppointmentReminder;
use App\Jobs\SendWeeklyMilestone;
use App\Models\Appointment;
use App\Models\Device;
use App\Models\Pregnancy;
use App\Models\User;
use App\Services\Push\PushSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\FakePushSender;
use Tests\TestCase;

/**
 * Gebelik kapatıldıktan sonra hiçbir bildirimin gitmemesi.
 *
 * Planın en başından beri merkezdeki kural bu. Kaybettiği gebeliğin haftalık
 * bildirimini almak, insanların uygulamayı sildiği andır — ve kuyruğa çoktan
 * girmiş bir iş, kapatma anından sonra çalışabilir. Bu yüzden kontrol
 * gönderim anında yapılır, kuyruğa alma anında değil.
 */
class NotificationSilenceTest extends TestCase
{
    use RefreshDatabase;

    private FakePushSender $push;

    protected function setUp(): void
    {
        parent::setUp();

        $this->push = new FakePushSender;
        $this->app->instance(PushSender::class, $this->push);
    }

    private function pregnancyWithDevice(): Pregnancy
    {
        $user = User::factory()->create();
        Device::factory()->for($user)->create();

        return Pregnancy::factory()->for($user)->create([
            'method' => 'lmp',
            'input_date' => now()->subDays(168)->toDateString(),
        ]);
    }

    #[Test]
    public function an_active_pregnancy_receives_its_weekly_notification(): void
    {
        $pregnancy = $this->pregnancyWithDevice();

        (new SendWeeklyMilestone($pregnancy->id))->handle($this->push);

        $this->assertSame(1, $this->push->count());
        $this->assertStringContainsString('24. haftadasınız', $this->push->sent[0]['message']->title);
    }

    #[Test]
    public function a_queued_weekly_notification_is_silent_after_a_loss(): void
    {
        $pregnancy = $this->pregnancyWithDevice();

        // İş kuyruğa girdi, sonra kullanıcı gebeliği kapattı.
        $job = new SendWeeklyMilestone($pregnancy->id);
        $pregnancy->end('loss');

        $job->handle($this->push);

        $this->assertSame(0, $this->push->count());
    }

    #[Test]
    public function a_queued_appointment_reminder_is_silent_after_a_loss(): void
    {
        $pregnancy = $this->pregnancyWithDevice();
        $appointment = Appointment::factory()->for($pregnancy)->dueForReminder()->create();

        $job = new SendAppointmentReminder($appointment->id);
        $pregnancy->end('loss');

        $job->handle($this->push);

        $this->assertSame(0, $this->push->count());
        $this->assertNull($appointment->fresh()->reminded_at);
    }

    #[Test]
    public function a_completed_appointment_does_not_remind(): void
    {
        $pregnancy = $this->pregnancyWithDevice();
        $appointment = Appointment::factory()->for($pregnancy)->dueForReminder()->create([
            'completed_at' => now(),
        ]);

        (new SendAppointmentReminder($appointment->id))->handle($this->push);

        $this->assertSame(0, $this->push->count());
    }

    #[Test]
    public function the_dispatcher_skips_closed_pregnancies(): void
    {
        Queue::fake();

        $active = $this->pregnancyWithDevice();
        Appointment::factory()->for($active)->dueForReminder()->create();

        $closed = $this->pregnancyWithDevice();
        Appointment::factory()->for($closed)->dueForReminder()->create();
        $closed->end('birth');

        $this->artisan('app:dispatch-appointment-reminders')->assertSuccessful();

        Queue::assertPushed(SendAppointmentReminder::class, 1);
    }

    #[Test]
    public function the_weekly_dispatcher_only_fires_on_a_full_week(): void
    {
        Queue::fake();

        $user = User::factory()->create(['timezone' => 'Europe/Istanbul']);
        // 168 gün = tam 24 hafta.
        Pregnancy::factory()->for($user)->create([
            'method' => 'lmp',
            'input_date' => now('Europe/Istanbul')->subDays(168)->toDateString(),
        ]);

        // 170 gün = 24 hafta 2 gün: bugün hafta dönüşü değil.
        $other = User::factory()->create(['timezone' => 'Europe/Istanbul']);
        Pregnancy::factory()->for($other)->create([
            'method' => 'lmp',
            'input_date' => now('Europe/Istanbul')->subDays(170)->toDateString(),
        ]);

        $this->artisan('app:dispatch-weekly-milestones')->assertSuccessful();

        Queue::assertPushed(SendWeeklyMilestone::class, 1);
    }

    #[Test]
    public function a_reminder_is_only_sent_once(): void
    {
        $pregnancy = $this->pregnancyWithDevice();
        $appointment = Appointment::factory()->for($pregnancy)->dueForReminder()->create();

        (new SendAppointmentReminder($appointment->id))->handle($this->push);
        $this->assertSame(1, $this->push->count());
        $this->assertNotNull($appointment->fresh()->reminded_at);

        Queue::fake();
        $this->artisan('app:dispatch-appointment-reminders')->assertSuccessful();
        Queue::assertNothingPushed();
    }

    #[Test]
    public function a_user_without_a_device_is_skipped_quietly(): void
    {
        $user = User::factory()->create();
        $pregnancy = Pregnancy::factory()->for($user)->create([
            'method' => 'lmp',
            'input_date' => now()->subDays(168)->toDateString(),
        ]);

        (new SendWeeklyMilestone($pregnancy->id))->handle($this->push);

        $this->assertSame(0, $this->push->count());
    }
}
