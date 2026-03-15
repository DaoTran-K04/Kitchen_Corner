<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        // Danh sách thể loại của bạn
        $categories = [
            'Văn Học Nước Ngoài',
            'Văn Học Việt Nam',
            'Tâm Lý - Kỹ Năng Sống',
            'Sức Khỏe',
            'Thiếu Nhi',
            'Tiểu Thuyết',
            'Khoa Học Viễn Tưởng',
            'Ngoại Ngữ',
            'Nấu Ăn',
            'Kinh Dị',
            'Trinh Thám',
            'Công Nghệ Thông Tin',
            'Kinh Tế',
            'Kinh Doanh',
            'Lịch Sử',
            'Chính Trị - Xã Hội',
            'Nghệ Thuật',
            'Du Lịch',
            'Người Thành Công Đọc Gì',
            'Gia Đình - Mối Quan Hệ',
        ];

        $name = fake()->unique()->randomElement($categories) ?? fake()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => 'Tuyển tập những cuốn sách hay nhất về chủ đề ' . $name,
        ];
    }
}
