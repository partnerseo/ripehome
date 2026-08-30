<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\WeekContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WeekContent> */
class WeekContentFactory extends Factory
{
    protected $model = WeekContent::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'week' => fake()->unique()->numberBetween(1, 42),
            'locale' => 'tr',
            'baby_size_label' => 'mısır koçanı',
            'baby_length_mm' => 300,
            'baby_weight_g' => 600,
            'baby_body' => 'Bebeğin akciğerleri gelişmeye devam ediyor.',
            'mother_body' => 'Karın bölgesinde gerginlik hissedebilirsiniz.',
            'tips_body' => 'Bol su için ve düzenli yürüyüş yapın.',
            'status' => WeekContent::STATUS_DRAFT,
        ];
    }

    /** Gözden geçirilmiş ve yayında. */
    public function published(): static
    {
        return $this->state([
            'status' => WeekContent::STATUS_PUBLISHED,
            'reviewed_by' => 'Dr. Ayşe Yılmaz, Kadın Hastalıkları ve Doğum',
            'reviewed_at' => '2026-03-15',
        ]);
    }
}
