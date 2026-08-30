<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pregnancy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pregnancy>
 */
class PregnancyFactory extends Factory
{
    protected $model = Pregnancy::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'method' => 'lmp',
            // Yaklaşık 24. hafta.
            'input_date' => now()->subDays(168)->toDateString(),
            'cycle_length' => 28,
            'baby_count' => 1,
            'status' => Pregnancy::STATUS_ACTIVE,
        ];
    }
}
