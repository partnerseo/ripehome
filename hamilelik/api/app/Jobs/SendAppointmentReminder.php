<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\Push\PushMessage;
use App\Services\Push\PushSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAppointmentReminder implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly int $appointmentId) {}

    public function handle(PushSender $push): void
    {
        $appointment = Appointment::with('pregnancy.user.devices')->find($this->appointmentId);

        if ($appointment === null || $appointment->isCompleted()) {
            return;
        }

        // Kuyruğa girmiş bir iş, gönderim anında hâlâ geçerli mi diye bakar.
        // Gebelik kapatıldıysa — özellikle kayıpla — bu bildirim asla gitmemeli.
        if (! $appointment->pregnancy->isActive()) {
            return;
        }

        $tokens = $appointment->pregnancy->user->devices->pluck('expo_push_token')->all();

        if ($tokens === []) {
            return;
        }

        $push->send($tokens, new PushMessage(
            title: $appointment->title,
            body: $appointment->scheduled_at !== null
                ? 'Randevunuz yarın. Unutmayın!'
                : 'Bu tetkik için zaman aralığı bugün başlıyor.',
            data: ['type' => 'appointment', 'appointment_id' => $appointment->id],
        ));

        $appointment->forceFill(['reminded_at' => now()])->save();
    }
}
