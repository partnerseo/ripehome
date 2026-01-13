<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            [
                'name' => '%100 Pamuk',
                'color' => '#10B981',
                'is_active' => true,
            ],
            [
                'name' => 'Organik',
                'color' => '#22C55E',
                'is_active' => true,
            ],
            [
                'name' => 'Hipoalerjenik',
                'color' => '#3B82F6',
                'is_active' => true,
            ],
            [
                'name' => 'Antibakteriyel',
                'color' => '#8B5CF6',
                'is_active' => true,
            ],
            [
                'name' => 'Yumuşak Doku',
                'color' => '#EC4899',
                'is_active' => true,
            ],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}













