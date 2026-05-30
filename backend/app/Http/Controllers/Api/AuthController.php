<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\SecurityLog;
use App\Services\OtpService;
use App\Services\TelegramService;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        private OtpService      $otp,
        private TokenService    $tokens,
        private TelegramService $telegram,
    ) {}

    /**
     * POST /auth/send-otp
     * Sadece e-posta gerekli. Telefon kayıt adımında alınır.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:200'],
            'lang'  => ['nullable', 'string', 'in:tr,en,de,ru,ar'],
        ]);

        if ($v->fails()) {
            return response()->json(['message' => 'Geçerli bir e-posta adresi girin.', 'errors' => $v->errors()], 422);
        }

        $email = strtolower(trim($request->input('email')));
        $lang  = $request->input('lang', 'tr');
        $ip    = $request->ip();

        try {
            $this->otp->send($email, $ip, $lang);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Doğrulama kodu gönderilemedi. Lütfen tekrar deneyin.'], 500);
        }

        return response()->json([
            'message'    => 'Doğrulama kodu e-posta adresinize gönderildi.',
            'debug_code' => $this->otp->lastDebugCode,
        ]);
    }

    /**
     * POST /auth/verify-otp
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:200'],
            'kod'   => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ]);

        if ($v->fails()) {
            return response()->json(['message' => 'Geçersiz istek.'], 422);
        }

        $email = strtolower(trim($request->input('email')));
        $code  = $request->input('kod');
        $ip    = $request->ip();
        $ua    = $request->userAgent() ?? '';

        $result = $this->otp->verify($email, $code);

        if (! $result['ok']) {
            $member = Member::where('email', $email)->first();
            $this->log(SecurityLog::OLAY_FAILED_OTP, $ip, $ua, $member?->id, [
                'email'         => $email,
                'error'         => $result['error'],
                'attempts_left' => $result['attempts_left'] ?? null,
            ]);

            return response()->json([
                'message'       => $result['error'],
                'locked'        => $result['locked'],
                'attempts_left' => $result['attempts_left'] ?? null,
            ], 401);
        }

        $member = Member::where('email', $email)->first();

        if (! $member) {
            cache()->put("otp_verified_{$email}", true, now()->addMinutes(3));
            return response()->json([
                'needs_registration' => true,
                'email'              => $email,
            ]);
        }

        if ($member->banned_at) {
            return response()->json(['message' => 'Hesabınız askıya alınmış.'], 403);
        }

        if ($member->durum === 'beklemede') {
            $member->update(['durum' => 'onaylandi']);
        } elseif ($member->durum === 'pasif') {
            return response()->json(['message' => 'Hesabınız pasife alınmış. Lütfen bizimle iletişime geçin.'], 403);
        }

        $tokenData = $this->tokens->create($member, $ip, $ua);
        $this->log(SecurityLog::OLAY_LOGIN, $ip, $ua, $member->id);

        return response()->json([
            'needs_registration' => false,
            'token'              => $tokenData['token'],
            'expires_at'         => $tokenData['expires_at'],
            'member'             => $this->memberData($member),
        ]);
    }

    /**
     * POST /auth/register
     * E-posta OTP ile doğrulanmış, telefon burada alınır.
     */
    public function register(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'email'   => ['required', 'email', 'max:200'],
            'telefon' => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/'],
            'ad'      => ['required', 'string', 'max:100'],
            'soyad'   => ['required', 'string', 'max:100'],
            'firma'   => ['nullable', 'string', 'max:200'],
            'ulke'    => ['required', 'string', 'size:2'],
        ]);

        if ($v->fails()) {
            return response()->json(['message' => 'Lütfen tüm alanları doldurun.', 'errors' => $v->errors()], 422);
        }

        $email = strtolower(trim($request->input('email')));
        $ip    = $request->ip();
        $ua    = $request->userAgent() ?? '';

        if (! cache()->pull("otp_verified_{$email}")) {
            return response()->json(['message' => 'OTP doğrulaması gerekli.'], 403);
        }

        try {
            $member = DB::transaction(function () use ($request, $email) {
                if (Member::where('email', $email)->lockForUpdate()->exists()) {
                    throw new \RuntimeException('Bu e-posta adresi zaten kayıtlı. Giriş yapmak için e-posta adresinize doğrulama kodu gönderin.', 409);
                }

                $telefon = $request->input('telefon');
                if (Member::where('telefon', $telefon)->lockForUpdate()->exists()) {
                    throw new \RuntimeException('Bu telefon numarası zaten kayıtlı. Farklı bir numara deneyin veya mevcut hesabınızla giriş yapın.', 409);
                }

                return Member::create([
                    'email'   => $email,
                    'telefon' => $request->input('telefon'),
                    'ad'      => strip_tags($request->input('ad')),
                    'soyad'   => strip_tags($request->input('soyad')),
                    'firma'   => $request->input('firma') ? strip_tags($request->input('firma')) : null,
                    'ulke'    => strtoupper($request->input('ulke')),
                    'durum'   => 'onaylandi',
                ]);
            });
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 409);
        }

        $this->log(SecurityLog::OLAY_REGISTERED, $ip, $ua, $member->id);

        try {
            $this->telegram->sendMessage(
                "🆕 <b>Yeni Üye Kaydı</b>\n" .
                "👤 {$member->ad} {$member->soyad}\n" .
                ($member->firma ? "🏢 {$member->firma}\n" : '') .
                "📧 {$member->email}\n" .
                "📱 {$member->telefon}\n" .
                "🌍 {$member->ulke}\n" .
                "🕐 " . now()->format('d.m.Y H:i')
            );
        } catch (\Exception) {}

        $tokenData = $this->tokens->create($member, $ip, $ua);

        return response()->json([
            'message'    => 'Hoş geldiniz! Artık tüm toptan fiyatları görebilirsiniz.',
            'token'      => $tokenData['token'],
            'expires_at' => $tokenData['expires_at'],
            'member'     => $this->memberData($member),
        ], 201);
    }

    /**
     * POST /auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $raw    = $request->bearerToken() ?? $request->cookie('ripehome_member_token');
        $member = $request->attributes->get('member');
        $ip     = $request->ip();
        $ua     = $request->userAgent() ?? '';

        if ($raw) {
            $this->tokens->revoke($raw);
        }

        $this->log(SecurityLog::OLAY_LOGOUT, $ip, $ua, $member?->id);

        return response()->json(['message' => 'Çıkış yapıldı.'])
            ->withoutCookie('ripehome_member_token');
    }

    /**
     * GET /auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $member = $request->attributes->get('member');
        return response()->json(['member' => $this->memberData($member)]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function memberData(Member $member): array
    {
        return [
            'id'      => $member->id,
            'ad'      => $member->ad,
            'soyad'   => $member->soyad,
            'firma'   => $member->firma,
            'email'   => $member->email,
            'telefon' => $member->telefon,
            'ulke'    => $member->ulke,
            'durum'   => $member->durum,
        ];
    }

    private function log(string $olay, string $ip, string $ua, ?int $memberId = null, array $extra = []): void
    {
        SecurityLog::create([
            'member_id'  => $memberId,
            'olay'       => $olay,
            'ip'         => $ip,
            'user_agent' => substr($ua, 0, 500),
            'extra'      => empty($extra) ? null : $extra,
        ]);
    }
}
