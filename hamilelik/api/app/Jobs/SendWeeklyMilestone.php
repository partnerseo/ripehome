<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Pregnancy;
use App\Services\Push\PushMessage;
use App\Services\Push\PushSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWeeklyMilestone implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly int $pregnancyId) {}

    public function handle(PushSender $push): void
    {
        $pregnancy = Pregnancy::with('user.devices')->find($this->pregnancyId);

        // Kapanmış gebelik için haftalık bildirim gitmez. Kaybettiği bebeğin
        // "bu hafta şu kadar büyüdü" bildirimini almak, insanların uygulamayı
        // sildiği andır — bu kontrol o yüzden gönderim anında yapılır.
        if ($pregnancy === null || ! $pregnancy->isActive()) {
            return;
        }

        $ga = $pregnancy->gestationalAge();

        if ($ga['week'] < 4 || $ga['week'] > 42) {
            return;
        }

        $tokens = $pregnancy->user->devices->pluck('expo_push_token')->all();

        if ($tokens === []) {
            return;
        }

        $push->send($tokens, new PushMessage(
            title: "{$ga['week']}. haftadasınız",
            body: 'Bu haftanın gelişmelerine göz atın.',
            data: ['type' => 'week', 'week' => $ga['week']],
        ));
    }
}
