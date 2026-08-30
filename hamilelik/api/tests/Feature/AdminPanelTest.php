<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_panel_requires_a_login(): void
    {
        $this->get('/admin')->assertRedirectContains('/admin/login');
    }

    #[Test]
    public function an_admin_can_open_the_panel(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->get('/admin')->assertSuccessful();
    }

    #[Test]
    public function an_app_user_cannot_reach_the_panel(): void
    {
        // Hamile kullanıcının hesabı içerik paneline giriş yapamaz: ayrı guard,
        // ayrı tablo. Aynı e-posta olsa bile bağ kurulmaz.
        $user = User::factory()->create();

        $this->actingAs($user, 'web')->get('/admin')->assertRedirectContains('/admin/login');
    }

    #[Test]
    public function the_create_admin_command_enforces_a_long_password(): void
    {
        $this->artisan('app:create-admin', [
            '--name' => 'Editör',
            '--email' => 'editor@example.com',
            '--password' => 'kisa',
        ])->assertFailed();

        $this->assertDatabaseCount('admins', 0);
    }

    #[Test]
    public function the_create_admin_command_creates_an_admin(): void
    {
        $this->artisan('app:create-admin', [
            '--name' => 'Editör',
            '--email' => 'editor@example.com',
            '--password' => 'yeterince-uzun-parola',
        ])->assertSuccessful();

        $this->assertDatabaseHas('admins', ['email' => 'editor@example.com']);
        $this->assertNotSame('yeterince-uzun-parola', Admin::first()->password);
    }
}
