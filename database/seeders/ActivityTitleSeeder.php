<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityTitle;

class ActivityTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            [
                'name' => 'Tân binh góc bếp',
                'icon' => '🌱',
                'color' => '#22C55E', // green-500
                'min_posts' => 0,
                'min_recipes' => 0,
                'priority' => 1,
            ],
            [
                'name' => 'Tín đồ ẩm thực',
                'icon' => '🍽️',
                'color' => '#3B82F6', // blue-500
                'min_posts' => 3,
                'min_recipes' => 0,
                'priority' => 2,
            ],
            [
                'name' => 'Bếp phó',
                'icon' => '👨‍🍳',
                'color' => '#8B5CF6', // purple-500
                'min_posts' => 5,
                'min_recipes' => 1,
                'priority' => 3,
            ],
            [
                'name' => 'Bếp trưởng',
                'icon' => '👑',
                'color' => '#F97316', // orange-500
                'min_posts' => 10,
                'min_recipes' => 3,
                'priority' => 4,
            ],
            [
                'name' => 'Chuyên gia ẩm thực',
                'icon' => '🏆',
                'color' => '#EAB308', // yellow-500
                'min_posts' => 20,
                'min_recipes' => 5,
                'priority' => 5,
            ],
            [
                'name' => 'Vua đầu bếp',
                'icon' => '⭐',
                'color' => '#EF4444', // red-500
                'min_posts' => 50,
                'min_recipes' => 10,
                'priority' => 6,
            ],
        ];

        foreach ($titles as $title) {
            ActivityTitle::updateOrCreate(
                ['name' => $title['name']],
                $title
            );
        }

        $this->command->info('Activity titles seeded successfully!');
    }
}
