<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pregnancy;
use App\Models\PregnancyShare;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PregnancyShare> */
class PregnancyShareFactory extends Factory
{
    protected $model = PregnancyShare::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'pregnancy_id' => Pregnancy::factory(),
            'invited_email' => fake()->unique()->safeEmail(),
            'token' => PregnancyShare::newToken(),
        ];
    }
}
