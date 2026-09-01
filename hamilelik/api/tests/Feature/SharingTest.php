<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Pregnancy;
use App\Models\PregnancyShare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Eşin salt-okunur erişimi.
 *
 * En kritik testler yazma denemeleridir: paylaşım okuma verir, yazma vermez.
 */
class SharingTest extends TestCase
{
    use RefreshDatabase;

    private function ownerWithPregnancy(): array
    {
        $owner = User::factory()->create();
        $pregnancy = Pregnancy::factory()->for($owner)->create([
            'method' => 'lmp',
            'input_date' => now()->subDays(168)->toDateString(),
        ]);

        return [$owner, $pregnancy];
    }

    #[Test]
    public function the_owner_can_invite_and_the_partner_can_accept(): void
    {
        [$owner, $pregnancy] = $this->ownerWithPregnancy();
        $partner = User::factory()->create(['email' => 'es@example.com']);

        $this->actingAs($owner, 'sanctum');
        $token = $this->postJson('/api/v1/shares', ['email' => 'es@example.com'])
            ->assertCreated()
            ->json('data.token');

        $this->actingAs($partner, 'sanctum');
        $this->postJson('/api/v1/shares/accept', ['token' => $token])->assertOk();

        $this->getJson('/api/v1/shared-pregnancies')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.owner_email', $owner->email)
            ->assertJsonPath('data.0.pregnancy.gestational_age.display', '24h 0g');
    }

    #[Test]
    public function a_partner_cannot_write_anything(): void
    {
        [$owner, $pregnancy] = $this->ownerWithPregnancy();
        $partner = User::factory()->create(['email' => 'es@example.com']);

        PregnancyShare::factory()->for($pregnancy)->create([
            'invited_email' => $partner->email,
            'user_id' => $partner->id,
            'accepted_at' => now(),
        ]);

        $this->actingAs($partner, 'sanctum');

        // Paylaşım okuma verir; bu uçların hepsi sahiplik kontrolünden geçer.
        $this->postJson("/api/v1/pregnancies/{$pregnancy->id}/end", ['reason' => 'birth'])->assertStatus(404);
        $this->postJson("/api/v1/pregnancies/{$pregnancy->id}/redate", [
            'measured_on' => '2026-04-10', 'ga_days_at_measure' => 100,
        ])->assertStatus(404);

        // Eşin kendi aktif gebeliği olmadığı için yazma uçları da boş döner.
        $this->postJson('/api/v1/sync', [
            'health_logs' => [['client_uuid' => (string) Str::uuid(), 'type' => 'weight', 'value_1' => 70, 'measured_on' => '2026-06-22']],
        ])->assertStatus(422);

        $this->getJson('/api/v1/appointments')->assertOk()->assertJsonCount(0, 'data');
        $this->assertDatabaseHas('pregnancies', ['id' => $pregnancy->id, 'status' => 'active']);
    }

    #[Test]
    public function an_invite_can_only_be_accepted_by_the_invited_address(): void
    {
        [$owner, $pregnancy] = $this->ownerWithPregnancy();
        $share = PregnancyShare::factory()->for($pregnancy)->create(['invited_email' => 'es@example.com']);

        // Bağlantı paylaşılsa bile erişim davet edilen kişide kalır.
        $stranger = User::factory()->create(['email' => 'yabanci@example.com']);
        $this->actingAs($stranger, 'sanctum');

        $this->postJson('/api/v1/shares/accept', ['token' => $share->token])
            ->assertStatus(403)
            ->assertJsonPath('code', 'invite_email_mismatch');

        $this->getJson('/api/v1/shared-pregnancies')->assertOk()->assertJsonCount(0, 'data');
    }

    #[Test]
    public function revoking_a_share_cuts_access_immediately(): void
    {
        [$owner, $pregnancy] = $this->ownerWithPregnancy();
        $partner = User::factory()->create(['email' => 'es@example.com']);
        $share = PregnancyShare::factory()->for($pregnancy)->create([
            'invited_email' => $partner->email, 'user_id' => $partner->id, 'accepted_at' => now(),
        ]);

        $this->actingAs($partner, 'sanctum');
        $this->getJson('/api/v1/shared-pregnancies')->assertJsonCount(1, 'data');

        $this->actingAs($owner, 'sanctum');
        $this->deleteJson("/api/v1/shares/{$share->id}")->assertNoContent();

        $this->actingAs($partner, 'sanctum');
        $this->getJson('/api/v1/shared-pregnancies')->assertOk()->assertJsonCount(0, 'data');
    }

    #[Test]
    public function a_closed_pregnancy_disappears_from_the_partners_view(): void
    {
        [$owner, $pregnancy] = $this->ownerWithPregnancy();
        $partner = User::factory()->create(['email' => 'es@example.com']);
        PregnancyShare::factory()->for($pregnancy)->create([
            'invited_email' => $partner->email, 'user_id' => $partner->id, 'accepted_at' => now(),
        ]);

        $pregnancy->end('loss');

        // Kayıp sonrası eşin ekranında da hafta ve geri sayım kalmaz.
        $this->actingAs($partner, 'sanctum');
        $this->getJson('/api/v1/shared-pregnancies')->assertOk()->assertJsonCount(0, 'data');
    }

    #[Test]
    public function only_the_owner_can_revoke(): void
    {
        [$owner, $pregnancy] = $this->ownerWithPregnancy();
        $share = PregnancyShare::factory()->for($pregnancy)->create();

        $this->actingAs(User::factory()->create(), 'sanctum');
        $this->deleteJson("/api/v1/shares/{$share->id}")->assertStatus(404);

        $this->assertNull($share->fresh()->revoked_at);
    }

    #[Test]
    public function inviting_the_same_address_twice_reuses_the_record(): void
    {
        [$owner] = $this->ownerWithPregnancy();
        $this->actingAs($owner, 'sanctum');

        $this->postJson('/api/v1/shares', ['email' => 'es@example.com'])->assertCreated();
        $this->postJson('/api/v1/shares', ['email' => 'es@example.com'])->assertCreated();

        $this->assertDatabaseCount('pregnancy_shares', 1);
    }

    #[Test]
    public function you_cannot_invite_yourself(): void
    {
        [$owner] = $this->ownerWithPregnancy();
        $this->actingAs($owner, 'sanctum');

        $this->postJson('/api/v1/shares', ['email' => $owner->email])
            ->assertStatus(422)
            ->assertJsonPath('code', 'self_invite');
    }
}
