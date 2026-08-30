<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;
use App\Models\Pregnancy;
use App\Models\ScreeningTemplate;
use Illuminate\Support\Carbon;

/**
 * Tetkik şablonlarından gebeliğe özel randevu penceresi üretir.
 *
 * Pencereler tarih olarak yazılır: takvim ekranı her açılışta yeniden hesap
 * yapmasın. Bunun bedeli, SAT değişince (USG ile yeniden tarihleme) pencerelerin
 * yeniden hesaplanması gerektiğidir — replan() bunu yapar.
 */
class AppointmentPlanner
{
    /** Randevu saati girilmemişse pencerenin açıldığı gün bu saatte hatırlatılır. */
    private const WINDOW_REMINDER_HOUR = 9;

    /** Randevu saati girilmişse kaç gün önce hatırlatılır. */
    private const DAYS_BEFORE_APPOINTMENT = 1;

    /**
     * Gebelik için eksik olan otomatik randevuları üretir.
     *
     * Yalnızca yayındaki (hekim onaylı) şablonlardan üretir: onaysız bir tetkik
     * takvimi kullanıcıya randevu olarak da görünmemeli.
     *
     * @return int Üretilen kayıt sayısı.
     */
    public function plan(Pregnancy $pregnancy): int
    {
        if (! $pregnancy->isActive()) {
            return 0;
        }

        $existing = $pregnancy->appointments()
            ->whereNotNull('screening_template_id')
            ->pluck('screening_template_id')
            ->all();

        $templates = ScreeningTemplate::query()
            ->published()
            ->where('country', 'TR')
            ->where('locale', $pregnancy->user->locale ?? 'tr')
            ->whereNotIn('id', $existing)
            ->orderBy('sort')
            ->get();

        foreach ($templates as $template) {
            $pregnancy->appointments()->create([
                'screening_template_id' => $template->id,
                'title' => $template->name,
                'category' => $template->category,
                'description' => $template->description,
                'is_optional' => $template->is_optional,
                'window_start_week' => $template->week_start,
                'window_end_week' => $template->week_end,
                'window_start_on' => $this->weekStart($pregnancy, $template->week_start),
                'window_end_on' => $this->weekEnd($pregnancy, $template->week_end),
                'reminder_at' => $this->windowReminder($pregnancy, $template->week_start),
                'source' => Appointment::SOURCE_AUTO,
            ]);
        }

        return $templates->count();
    }

    /**
     * SAT değiştikten sonra pencereleri yeniden hesaplar.
     *
     * Kullanıcının aldığı gerçek randevuya (scheduled_at) dokunulmaz: o tarih
     * hastaneyle konuşulup alınmıştır, gebelik haftası düzeltildi diye kaymaz.
     */
    public function replan(Pregnancy $pregnancy): void
    {
        $appointments = $pregnancy->appointments()
            ->where('source', Appointment::SOURCE_AUTO)
            ->whereNull('completed_at')
            ->get();

        foreach ($appointments as $appointment) {
            if ($appointment->window_start_week === null || $appointment->window_end_week === null) {
                continue;
            }

            $appointment->window_start_on = $this->weekStart($pregnancy, $appointment->window_start_week);
            $appointment->window_end_on = $this->weekEnd($pregnancy, $appointment->window_end_week);

            if ($appointment->scheduled_at === null) {
                $appointment->reminder_at = $this->windowReminder($pregnancy, $appointment->window_start_week);
            }

            $appointment->save();
        }
    }

    /** Kullanıcı randevuya tarih verdiğinde hatırlatma o tarihe göre kurulur. */
    public function reminderForSchedule(Carbon $scheduledAt): Carbon
    {
        return $scheduledAt->copy()->subDays(self::DAYS_BEFORE_APPOINTMENT);
    }

    private function weekStart(Pregnancy $pregnancy, int $week): Carbon
    {
        return $pregnancy->lmp_date->copy()->addDays($week * 7);
    }

    /** Pencere, bitiş haftasının son gününde kapanır (o haftanın 6. günü). */
    private function weekEnd(Pregnancy $pregnancy, int $week): Carbon
    {
        return $pregnancy->lmp_date->copy()->addDays($week * 7 + 6);
    }

    /**
     * Pencerenin açıldığı gün, kullanıcının kendi sabahında hatırlatılır.
     * Sunucu UTC'de olduğu için bu dönüşüm atlanırsa bildirim gece yarısına düşer.
     */
    private function windowReminder(Pregnancy $pregnancy, int $week): Carbon
    {
        $timezone = $pregnancy->user->timezone ?? config('app.timezone');

        return Carbon::parse(
            $this->weekStart($pregnancy, $week)->toDateString(),
            $timezone,
        )->setTime(self::WINDOW_REMINDER_HOUR, 0)->utc();
    }
}
