<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ScreeningTemplate;
use App\Models\User;
use App\Models\WeekContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WeekContentApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    #[Test]
    public function it_serves_published_week_content(): void
    {
        $this->actingAsUser();
        WeekContent::factory()->published()->create(['week' => 24, 'baby_size_label' => 'mısır koçanı']);

        $this->getJson('/api/v1/weeks/24')
            ->assertOk()
            ->assertJsonPath('data.week', 24)
            ->assertJsonPath('data.baby_size_label', 'mısır koçanı')
            ->assertJsonPath('data.review.reviewed_by', 'Dr. Ayşe Yılmaz, Kadın Hastalıkları ve Doğum')
            ->assertJsonPath('data.review.reviewed_at', '2026-03-15');
    }

    #[Test]
    public function draft_content_is_never_served(): void
    {
        $this->actingAsUser();
        WeekContent::factory()->create(['week' => 24]);

        // Gözden geçirilmemiş metin kullanıcıya gitmez.
        $this->getJson('/api/v1/weeks/24')
            ->assertStatus(404)
            ->assertJsonPath('code', 'week_content_unavailable');
    }

    #[Test]
    public function content_that_lost_its_review_disappears_from_the_api(): void
    {
        $this->actingAsUser();
        $content = WeekContent::factory()->published()->create(['week' => 24]);

        $this->getJson('/api/v1/weeks/24')->assertOk();

        $content->update(['baby_body' => 'Onaysız yeni metin.']);

        $this->getJson('/api/v1/weeks/24')->assertStatus(404);
    }

    #[Test]
    public function the_bulk_endpoint_returns_only_published_weeks(): void
    {
        $this->actingAsUser();
        WeekContent::factory()->published()->create(['week' => 12]);
        WeekContent::factory()->published()->create(['week' => 24]);
        WeekContent::factory()->create(['week' => 30]);

        $this->getJson('/api/v1/weeks')
            ->assertOk()
            ->assertJsonPath('meta.count', 2)
            ->assertJsonCount(2, 'data');
    }

    #[Test]
    public function the_bulk_endpoint_returns_304_when_nothing_changed(): void
    {
        $this->actingAsUser();
        WeekContent::factory()->published()->create(['week' => 24]);

        $etag = $this->getJson('/api/v1/weeks')->assertOk()->headers->get('ETag');

        // Çevrimdışı ön yükleme: uygulama her açılışta tüm paketi değil,
        // yalnızca "değişti mi" sorusunu taşır.
        $this->withHeaders(['If-None-Match' => $etag])
            ->getJson('/api/v1/weeks')
            ->assertStatus(304);
    }

    #[Test]
    public function the_etag_changes_when_content_changes(): void
    {
        $this->actingAsUser();
        $content = WeekContent::factory()->published()->create(['week' => 24]);

        $first = $this->getJson('/api/v1/weeks')->headers->get('ETag');

        WeekContent::factory()->published()->create(['week' => 25]);

        $this->assertNotSame($first, $this->getJson('/api/v1/weeks')->headers->get('ETag'));
        $this->assertSame(2, WeekContent::published()->count());
        $this->assertTrue($content->fresh()->isPublished());
    }

    #[Test]
    public function content_is_served_per_locale(): void
    {
        $this->actingAsUser(['locale' => 'tr']);
        WeekContent::factory()->published()->create(['week' => 24, 'locale' => 'tr']);
        WeekContent::factory()->published()->create(['week' => 24, 'locale' => 'en']);

        $this->getJson('/api/v1/weeks/24')->assertOk()->assertJsonPath('data.locale', 'tr');
        $this->getJson('/api/v1/weeks/24?locale=en')->assertOk()->assertJsonPath('data.locale', 'en');
    }

    #[Test]
    public function screenings_only_expose_reviewed_templates(): void
    {
        $this->actingAsUser();
        ScreeningTemplate::factory()->published()->create(['code' => 'ogtt']);
        ScreeningTemplate::factory()->create(['code' => 'nt_ikili']);

        $this->getJson('/api/v1/screenings')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', 'ogtt');
    }

    #[Test]
    public function content_endpoints_require_authentication(): void
    {
        $this->getJson('/api/v1/weeks')->assertStatus(401);
        $this->getJson('/api/v1/weeks/24')->assertStatus(401);
        $this->getJson('/api/v1/screenings')->assertStatus(401);
    }
}
