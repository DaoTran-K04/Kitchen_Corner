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
                'name' => 'Thành viên mới',
                'icon' => '🌱',
                'color' => '#22C55E', // green-500
                'min_posts' => 0,
                'min_books' => 0,
                'priority' => 1,
            ],
            [
                'name' => 'Người đọc tích cực',
                'icon' => '📖',
                'color' => '#3B82F6', // blue-500
                'min_posts' => 3,
                'min_books' => 0,
                'priority' => 2,
            ],
            [
                'name' => 'Tác giả tập sự',
                'icon' => '✍️',
                'color' => '#8B5CF6', // purple-500
                'min_posts' => 5,
                'min_books' => 1,
                'priority' => 3,
            ],
            [
                'name' => 'Cộng tác viên',
                'icon' => '🤝',
                'color' => '#F97316', // orange-500
                'min_posts' => 10,
                'min_books' => 3,
                'priority' => 4,
            ],
            [
                'name' => 'Nhà phê bình',
                'icon' => '🏆',
                'color' => '#EAB308', // yellow-500
                'min_posts' => 20,
                'min_books' => 5,
                'priority' => 5,
            ],
            [
                'name' => 'Cây bút vàng',
                'icon' => '⭐',
                'color' => '#EF4444', // red-500
                'min_posts' => 50,
                'min_books' => 10,
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
