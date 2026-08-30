<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\ScreeningTemplate;
use App\Models\User;
use App\Models\WeekContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::factory()->create(['password' => 'yeterince-uzun-parola']);
    }

    #[Test]
    public function the_panel_requires_a_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/giris');
        $this->get('/admin/haftalar')->assertRedirect('/admin/giris');
        $this->get('/admin/onay')->assertRedirect('/admin/giris');
    }

    #[Test]
    public function an_admin_can_sign_in(): void
    {
        $admin = $this->admin();

        $this->post('/admin/giris', [
            'email' => $admin->email,
            'password' => 'yeterince-uzun-parola',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    #[Test]
    public function a_wrong_password_does_not_say_which_field_was_wrong(): void
    {
        $admin = $this->admin();

        $response = $this->from('/admin/giris')->post('/admin/giris', [
            'email' => $admin->email,
            'password' => 'yanlis-parola',
        ]);

        // E-posta varlığı sızdırılmaz: mesaj her iki durumda da aynı.
        $response->assertSessionHasErrors(['email' => 'E-posta veya parola hatalı.']);
        $this->assertGuest('admin');
    }

    #[Test]
    public function an_app_user_cannot_sign_in_to_the_panel(): void
    {
        // Hamile kullanıcının hesabı ayrı tabloda; panel onu tanımaz.
        $user = User::factory()->create(['email' => 'anne@example.com']);

        $this->post('/admin/giris', ['email' => $user->email, 'password' => 'herhangi'])
            ->assertSessionHasErrors('email');

        $this->assertGuest('admin');
        $this->actingAs($user, 'web')->get('/admin')->assertRedirect('/admin/giris');
    }

    #[Test]
    public function every_panel_screen_renders(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $week = WeekContent::factory()->create(['week' => 24]);
        $screening = ScreeningTemplate::factory()->create(['code' => 'ogtt']);

        $this->get('/admin')->assertOk()->assertSee('Neyin eksik olduğu');
        $this->get('/admin/haftalar')->assertOk()->assertSee('Hafta içerikleri');
        $this->get('/admin/haftalar/yeni')->assertOk();
        $this->get("/admin/haftalar/{$week->id}")->assertOk();
        $this->get('/admin/tetkikler')->assertOk()->assertSee('Tetkik takvimi');
        $this->get('/admin/tetkikler/yeni')->assertOk();
        $this->get("/admin/tetkikler/{$screening->id}")->assertOk();
        $this->get('/admin/onay')->assertOk()->assertSee('Onay kuyruğu');
        $this->get("/admin/onay/hafta/{$week->id}")->assertOk();
        $this->get("/admin/onay/tetkik/{$screening->id}")->assertOk();
    }

    #[Test]
    public function an_editor_can_create_week_content(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $this->post('/admin/haftalar', [
            'week' => 24,
            'locale' => 'tr',
            'baby_size_label' => 'mısır koçanı',
            'baby_body' => 'Bebeğin iç kulağı gelişti.',
            'status' => 'draft',
        ])->assertRedirect();

        $this->assertDatabaseHas('week_contents', ['week' => 24, 'status' => 'draft']);
    }

    #[Test]
    public function publishing_without_a_reviewer_is_refused_with_a_readable_message(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $response = $this->from('/admin/haftalar/yeni')->post('/admin/haftalar', [
            'week' => 24,
            'locale' => 'tr',
            'baby_body' => 'Metin.',
            'status' => 'published',
        ]);

        // Kullanıcı 500 değil, alanın yanında mesaj görür.
        $response->assertSessionHasErrors(['reviewed_by', 'reviewed_at']);
        $this->assertDatabaseCount('week_contents', 0);
    }

    #[Test]
    public function the_same_week_cannot_be_created_twice_in_one_locale(): void
    {
        $this->actingAs($this->admin(), 'admin');
        WeekContent::factory()->create(['week' => 24, 'locale' => 'tr']);

        $this->post('/admin/haftalar', ['week' => 24, 'locale' => 'tr', 'status' => 'draft'])
            ->assertSessionHasErrors('week');

        // Aynı hafta başka dilde serbest.
        $this->post('/admin/haftalar', ['week' => 24, 'locale' => 'en', 'status' => 'draft'])
            ->assertSessionHasNoErrors();
    }

    #[Test]
    public function editing_published_text_warns_that_the_review_was_revoked(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $content = WeekContent::factory()->published()->create(['week' => 24]);

        $response = $this->put("/admin/haftalar/{$content->id}", [
            'week' => 24,
            'locale' => 'tr',
            'baby_body' => 'Onaydan sonra değiştirilmiş metin.',
            'status' => 'published',
            'reviewed_by' => $content->reviewed_by,
            'reviewed_at' => $content->reviewed_at->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertSame('draft', $content->fresh()->status);
        $response->assertSessionHas('warn');
    }

    #[Test]
    public function the_doctor_can_approve_from_the_review_queue(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $content = WeekContent::factory()->create(['week' => 24, 'status' => 'in_review']);

        $this->post("/admin/onay/hafta/{$content->id}", [
            'reviewed_by' => 'Dr. Ayşe Yılmaz',
            'reviewed_at' => '2026-03-15',
        ])->assertRedirect('/admin/onay');

        $fresh = $content->fresh();
        $this->assertTrue($fresh->isPublished());
        $this->assertSame('Dr. Ayşe Yılmaz', $fresh->reviewed_by);
    }

    #[Test]
    public function approving_without_a_name_is_refused(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $content = WeekContent::factory()->create(['week' => 24, 'status' => 'in_review']);

        $this->from("/admin/onay/hafta/{$content->id}")
            ->post("/admin/onay/hafta/{$content->id}", ['reviewed_at' => '2026-03-15'])
            ->assertSessionHasErrors('reviewed_by');

        $this->assertFalse($content->fresh()->isPublished());
    }

    #[Test]
    public function the_review_queue_only_lists_unpublished_records(): void
    {
        $this->actingAs($this->admin(), 'admin');

        WeekContent::factory()->create(['week' => 12, 'status' => 'in_review']);
        WeekContent::factory()->published()->create(['week' => 24]);

        $this->get('/admin/onay')
            ->assertOk()
            ->assertSee('12. hafta')
            ->assertDontSee('24. hafta');
    }

    #[Test]
    public function signing_out_ends_the_session(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $this->post('/admin/cikis')->assertRedirect('/admin/giris');
        $this->assertGuest('admin');
    }

    #[Test]
    public function the_create_admin_command_enforces_a_long_password(): void
    {
        $this->artisan('app:create-admin', [
            '--name' => 'Editör', '--email' => 'editor@example.com', '--password' => 'kisa',
        ])->assertFailed();

        $this->assertDatabaseCount('admins', 0);
    }

    #[Test]
    public function the_create_admin_command_creates_an_admin(): void
    {
        $this->artisan('app:create-admin', [
            '--name' => 'Editör', '--email' => 'editor@example.com', '--password' => 'yeterince-uzun-parola',
        ])->assertSuccessful();

        $this->assertDatabaseHas('admins', ['email' => 'editor@example.com']);
        $this->assertNotSame('yeterince-uzun-parola', Admin::first()->password);
    }
}
