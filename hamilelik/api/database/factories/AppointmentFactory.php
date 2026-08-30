<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Pregnancy;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Appointment> */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'pregnancy_id' => Pregnancy::factory(),
            'title' => 'Şeker yükleme testi',
            'category' => 'lab',
            'window_start_week' => 24,
            'window_end_week' => 28,
            'source' => Appointment::SOURCE_AUTO,
        ];
    }

    public function dueForReminder(): static
    {
        return $this->state(['reminder_at' => now()->subMinutes(5), 'reminded_at' => null]);
    }
}
