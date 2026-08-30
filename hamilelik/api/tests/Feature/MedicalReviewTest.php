<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Exceptions\MedicalReviewRequired;
use App\Models\ScreeningTemplate;
use App\Models\WeekContent;
use Database\Seeders\ScreeningTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tıbbi içeriğin yayına çıkma kapısı.
 *
 * Buradaki testler kırılırsa gözden geçirilmemiş bir tetkik haftası veya
 * belirti metni kullanıcıya gidebilir demektir.
 */
class MedicalReviewTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function content_cannot_be_published_without_a_reviewer(): void
    {
        $this->expectException(MedicalReviewRequired::class);

        WeekContent::factory()->create(['status' => WeekContent::STATUS_PUBLISHED]);
    }

    #[Test]
    public function content_cannot_be_published_with_a_reviewer_but_no_date(): void
    {
        $this->expectException(MedicalReviewRequired::class);

        WeekContent::factory()->create([
            'status' => WeekContent::STATUS_PUBLISHED,
            'reviewed_by' => 'Dr. Ayşe Yılmaz',
            'reviewed_at' => null,
        ]);
    }

    #[Test]
    public function screening_templates_obey_the_same_gate(): void
    {
        $this->expectException(MedicalReviewRequired::class);

        ScreeningTemplate::factory()->create(['status' => ScreeningTemplate::STATUS_PUBLISHED]);
    }

    #[Test]
    public function a_reviewed_record_publishes(): void
    {
        $content = WeekContent::factory()->published()->create();

        $this->assertTrue($content->isPublished());
        $this->assertTrue($content->hasReview());
    }

    #[Test]
    public function editing_published_medical_text_revokes_the_review(): void
    {
        $content = WeekContent::factory()->published()->create();

        $content->update(['baby_body' => 'Yeni metin.']);

        // Eski onay bu metni kapsamıyor: kayıt taslağa düşer.
        $this->assertSame(WeekContent::STATUS_DRAFT, $content->fresh()->status);
        $this->assertNull($content->fresh()->reviewed_by);
        $this->assertStringContainsString('önceki onay geçersiz', $content->fresh()->review_note);
    }

    #[Test]
    public function changing_a_screening_week_revokes_the_review(): void
    {
        $template = ScreeningTemplate::factory()->published()->create(['week_start' => 24, 'week_end' => 28]);

        // Yanlış bir hafta kaçırılmış tarama demektir: yeniden onay gerekir.
        $template->update(['week_start' => 20]);

        $this->assertSame(ScreeningTemplate::STATUS_DRAFT, $template->fresh()->status);
    }

    #[Test]
    public function a_fresh_review_in_the_same_save_keeps_it_published(): void
    {
        $content = WeekContent::factory()->published()->create();

        $content->update([
            'baby_body' => 'Hekimin yeniden okuduğu metin.',
            'reviewed_by' => 'Dr. Mehmet Kaya',
            'reviewed_at' => '2026-06-01',
        ]);

        $this->assertSame(WeekContent::STATUS_PUBLISHED, $content->fresh()->status);
        $this->assertSame('Dr. Mehmet Kaya', $content->fresh()->reviewed_by);
    }

    #[Test]
    public function non_medical_edits_do_not_revoke_the_review(): void
    {
        $content = WeekContent::factory()->published()->create();

        // source_refs onayın kapsamındaki metni değiştirmez.
        $content->update(['source_refs' => [['label' => 'WHO', 'url' => null]]]);

        $this->assertSame(WeekContent::STATUS_PUBLISHED, $content->fresh()->status);
    }

    #[Test]
    public function the_seeded_turkish_calendar_starts_unreviewed(): void
    {
        $this->seed(ScreeningTemplateSeeder::class);

        $this->assertSame(9, ScreeningTemplate::count());
        $this->assertSame(0, ScreeningTemplate::published()->count());
    }
}
