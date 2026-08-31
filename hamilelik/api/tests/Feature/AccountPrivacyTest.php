<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Consent;
use App\Models\Device;
use App\Models\Pregnancy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * KVKK gereklilikleri: açık rıza, dışa aktarma, kalıcı silme.
 * Üçü de destek talebi gerektirmeden uygulama içinden yapılabilmeli.
 */
class AccountPrivacyTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    #[Test]
    public function a_new_user_has_no_consent(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/v1/me')->assertOk()->assertJsonPath('user.has_consent', false);
    }

    #[Test]
    public function consent_is_recorded_with_its_version(): void
    {
        $user = $this->actingAsUser();

        $this->postJson('/api/v1/consents')->assertCreated()
            ->assertJsonPath('data.version', Consent::CURRENT_VERSION);

        $this->getJson('/api/v1/me')->assertJsonPath('user.has_consent', true);
        $this->assertDatabaseHas('consents', ['user_id' => $user->id, 'version' => Consent::CURRENT_VERSION]);
    }

    #[Test]
    public function consent_to_an_older_version_does_not_count(): void
    {
        $user = $this->actingAsUser();
        $user->consents()->create(['version' => 'kvkk-2020-01', 'accepted_at' => now()]);

        // Metin değiştiyse yeniden rıza istenir.
        $this->getJson('/api/v1/me')->assertJsonPath('user.has_consent', false);
    }

    #[Test]
    public function accepting_twice_does_not_duplicate_the_record(): void
    {
        $user = $this->actingAsUser();

        $this->postJson('/api/v1/consents')->assertCreated();
        $this->postJson('/api/v1/consents')->assertCreated();

        $this->assertSame(1, $user->consents()->count());
    }

    #[Test]
    public function the_export_contains_every_kind_of_record(): void
    {
        $user = $this->actingAsUser();
        $this->postJson('/api/v1/consents');

        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => now()->subDays(168)->toDateString()])
            ->assertCreated();

        $this->postJson('/api/v1/sync', [
            'health_logs' => [['client_uuid' => (string) Str::uuid(), 'type' => 'weight', 'value_1' => 68.4, 'measured_on' => '2026-06-22']],
            'symptom_logs' => [['client_uuid' => (string) Str::uuid(), 'logged_on' => '2026-06-22', 'symptoms' => ['bulanti'], 'mood' => 4]],
        ])->assertOk();

        $export = $this->getJson('/api/v1/me/export')->assertOk();

        $export->assertJsonPath('user.email', $user->email)
            ->assertJsonCount(1, 'consents')
            ->assertJsonCount(1, 'pregnancies')
            ->assertJsonCount(1, 'pregnancies.0.health_logs')
            ->assertJsonCount(1, 'pregnancies.0.symptom_logs')
            ->assertJsonPath('pregnancies.0.health_logs.0.unit', 'kg');
    }

    #[Test]
    public function deleting_the_account_requires_typing_the_email(): void
    {
        $user = $this->actingAsUser();

        $this->deleteJson('/api/v1/me', ['confirm_email' => 'baska@example.com'])
            ->assertStatus(422)
            ->assertJsonPath('code', 'confirmation_mismatch');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    #[Test]
    public function deleting_the_account_removes_everything(): void
    {
        $user = $this->actingAsUser();
        Device::factory()->for($user)->create();
        $this->postJson('/api/v1/consents');
        $this->postJson('/api/v1/pregnancies', ['method' => 'lmp', 'input_date' => now()->subDays(168)->toDateString()]);
        $this->postJson('/api/v1/sync', [
            'health_logs' => [['client_uuid' => (string) Str::uuid(), 'type' => 'weight', 'value_1' => 68.4, 'measured_on' => '2026-06-22']],
        ]);

        $this->deleteJson('/api/v1/me', ['confirm_email' => $user->email])->assertOk();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('pregnancies', 0);
        $this->assertDatabaseCount('health_logs', 0);
        $this->assertDatabaseCount('consents', 0);
        // Silinen hesaba bildirim gönderilecek bir yol kalmamalı.
        $this->assertDatabaseCount('devices', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function a_user_cannot_export_or_delete_another_account(): void
    {
        $other = User::factory()->create();
        Pregnancy::factory()->for($other)->create();

        $user = $this->actingAsUser();

        // Dışa aktarma her zaman yalnızca oturumdaki kullanıcıyı kapsar.
        $this->getJson('/api/v1/me/export')->assertOk()->assertJsonPath('user.email', $user->email);

        $this->deleteJson('/api/v1/me', ['confirm_email' => $other->email])->assertStatus(422);
        $this->assertDatabaseHas('users', ['id' => $other->id]);
    }

    #[Test]
    public function privacy_endpoints_require_authentication(): void
    {
        $this->postJson('/api/v1/consents')->assertStatus(401);
        $this->getJson('/api/v1/me/export')->assertStatus(401);
        $this->deleteJson('/api/v1/me')->assertStatus(401);
    }
}
