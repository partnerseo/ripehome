<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Randevu hatırlatmaları saat başı taranır; haftalık bildirim günde bir kez,
// kullanıcıların çoğunun uyanık olduğu saatte kuyruğa alınır.
Schedule::command('app:dispatch-appointment-reminders')->hourly()->withoutOverlapping();
Schedule::command('app:dispatch-weekly-milestones')->dailyAt('06:00')->withoutOverlapping();
