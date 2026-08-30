<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ScreeningTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ScreeningTemplate> */
class ScreeningTemplateFactory extends Factory
{
    protected $model = ScreeningTemplate::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->slug(2),
            'name' => 'Şeker yükleme testi',
            'description' => 'Gebelik şekeri taraması.',
            'category' => 'lab',
            'week_start' => 24,
            'week_end' => 28,
            'is_optional' => false,
            'status' => ScreeningTemplate::STATUS_DRAFT,
        ];
    }

    public function published(): static
    {
        return $this->state([
            'status' => ScreeningTemplate::STATUS_PUBLISHED,
            'reviewed_by' => 'Dr. Ayşe Yılmaz, Kadın Hastalıkları ve Doğum',
            'reviewed_at' => '2026-03-15',
        ]);
    }
}
