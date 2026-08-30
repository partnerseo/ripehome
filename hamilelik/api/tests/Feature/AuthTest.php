<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\OtpCodeMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function requesting_a_code_never_returns_it(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/auth/otp/request', ['email' => 'anne@example.com']);

        $response->assertStatus(202);

        // Açık kod hiçbir alanda dönmemeli — hata ayıklama kolaylığı için bile.
        $body = $response->getContent();
        $code = $this->captureCode();
        $this->assertStringNotContainsString($code, $body);
        $this->assertArrayNotHasKey('debug_code', $response->json());
        $this->assertArrayNotHasKey('code', $response->json());
    }

    #[Test]
    public function the_code_is_stored_hashed(): void
    {
        Mail::fake();
        $this->postJson('/api/v1/auth/otp/request', ['email' => 'anne@example.com']);

        $record = OtpCode::first();

        $this->assertNotSame($this->captureCode(), $record->code_hash);
        $this->assertStringStartsWith('$2y$', $record->code_hash);
    }

    #[Test]
    public function a_valid_code_creates_the_user_and_returns_a_token(): void
    {
        Mail::fake();
        $this->postJson('/api/v1/auth/otp/request', ['email' => 'anne@example.com']);

        $response = $this->postJson('/api/v1/auth/otp/verify', [
            'email' => 'anne@example.com',
            'code' => $this->captureCode(),
            'timezone' => 'Europe/Istanbul',
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'email', 'timezone']]);
        $this->assertDatabaseHas('users', ['email' => 'anne@example.com']);
    }

    #[Test]
    public function a_code_cannot_be_used_twice(): void
    {
        Mail::fake();
        $this->postJson('/api/v1/auth/otp/request', ['email' => 'anne@example.com']);
        $code = $this->captureCode();

        $this->postJson('/api/v1/auth/otp/verify', ['email' => 'anne@example.com', 'code' => $code])->assertOk();
        $this->postJson('/api/v1/auth/otp/verify', ['email' => 'anne@example.com', 'code' => $code])->assertStatus(422);
    }

    #[Test]
    public function a_wrong_code_is_rejected(): void
    {
        Mail::fake();
        $this->postJson('/api/v1/auth/otp/request', ['email' => 'anne@example.com']);
        $wrong = $this->captureCode() === '000000' ? '111111' : '000000';

        $this->postJson('/api/v1/auth/otp/verify', ['email' => 'anne@example.com', 'code' => $wrong])
            ->assertStatus(422);
        $this->assertDatabaseCount('users', 0);
    }

    #[Test]
    public function a_code_burns_after_five_wrong_attempts(): void
    {
        Mail::fake();
        $this->withoutMiddleware(ThrottleRequests::class);
        $this->postJson('/api/v1/auth/otp/request', ['email' => 'anne@example.com']);
        $code = $this->captureCode();
        $wrong = $code === '000000' ? '111111' : '000000';

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/otp/verify', ['email' => 'anne@example.com', 'code' => $wrong]);
        }

        // Doğru kod bile artık geçmez: deneme hakkı bitince kod yakılır.
        $this->postJson('/api/v1/auth/otp/verify', ['email' => 'anne@example.com', 'code' => $code])
            ->assertStatus(422);
    }

    #[Test]
    public function a_new_code_invalidates_the_previous_one(): void
    {
        Mail::fake();
        $this->postJson('/api/v1/auth/otp/request', ['email' => 'anne@example.com']);
        $first = $this->captureCode();

        $this->postJson('/api/v1/auth/otp/request', ['email' => 'anne@example.com']);

        $this->postJson('/api/v1/auth/otp/verify', ['email' => 'anne@example.com', 'code' => $first])
            ->assertStatus(422);
    }

    #[Test]
    public function the_email_rate_limit_actually_trips(): void
    {
        Mail::fake();

        // Rota sınırı devrede olmasın; burada servis sınırını ölçüyoruz.
        $this->withoutMiddleware(ThrottleRequests::class);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/otp/request', ['email' => 'anne@example.com'])->assertStatus(202);
        }

        $this->postJson('/api/v1/auth/otp/request', ['email' => 'anne@example.com'])->assertStatus(202);

        // Altıncı istek aynı yanıtı verir (varlık sızdırmamak için) ama kod üretmez.
        Mail::assertSentCount(5);
    }

    #[Test]
    public function protected_routes_require_a_token(): void
    {
        $this->getJson('/api/v1/me')->assertStatus(401);
        $this->getJson('/api/v1/pregnancies/current')->assertStatus(401);
    }

    #[Test]
    public function logout_revokes_the_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('mobile')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);

        // Guard cozulmus kullaniciyi onbellekte tutar; gercek istemcide her
        // istek yeni bir surectir, testte bunu elle taklit ediyoruz.
        $this->app['auth']->forgetGuards();

        $this->withToken($token)->getJson('/api/v1/me')->assertStatus(401);
    }

    /** Gönderilen e-postadan açık kodu okur — testin koda ulaşabildiği tek yer. */
    private function captureCode(): string
    {
        $code = null;

        Mail::assertSent(OtpCodeMail::class, function (OtpCodeMail $mail) use (&$code): bool {
            $code = $mail->code;

            return true;
        });

        return $code;
    }
}
