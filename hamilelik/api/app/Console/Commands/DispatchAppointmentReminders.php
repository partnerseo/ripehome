<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use App\Models\Pregnancy;
use Illuminate\Console\Command;

class DispatchAppointmentReminders extends Command
{
    protected $signature = 'app:dispatch-appointment-reminders';

    protected $description = 'Zamanı gelen randevu hatırlatmalarını kuyruğa alır';

    public function handle(): int
    {
        $count = 0;

        // Kapanmış gebeliklerin randevuları burada da elenir; iş sınıfı ayrıca
        // gönderim anında tekrar bakar. İki kat kontrol bilinçli: aradaki
        // sürede gebelik kapatılmış olabilir.
        Appointment::query()
            ->due(now())
            ->whereHas('pregnancy', fn ($q) => $q->where('status', Pregnancy::STATUS_ACTIVE))
            ->chunkById(200, function ($appointments) use (&$count): void {
                foreach ($appointments as $appointment) {
                    SendAppointmentReminder::dispatch($appointment->id);
                    $count++;
                }
            });

        $this->info("{$count} randevu hatırlatması kuyruğa alındı.");

        return self::SUCCESS;
    }
}
