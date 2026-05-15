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
        // Danh mục món ăn cho Góc Bếp
        $categories = [
            'Món chính',
            'Món khai vị',
            'Món tráng miệng',
            'Món ăn sáng',
            'Món ăn chay',
            'Món ăn kiêng - Giảm cân',
            'Salad & Gỏi',
            'Súp & Cháo',
            'Lẩu & Nướng',
            'Bánh ngọt - Dessert',
            'Đồ uống & Sinh tố',
            'Hải sản',
            'Thịt & Gia cầm',
            'Mì - Bún - Phở',
            'Cơm - Xôi',
            'Đồ ăn vặt',
            'Ẩm thực miền Bắc',
            'Ẩm thực miền Trung',
            'Ẩm thực miền Nam',
            'Ẩm thực quốc tế',
        ];

        $name = fake()->unique()->randomElement($categories) ?? fake()->word();

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => 'Tổng hợp những công thức nấu ăn ngon nhất về ' . $name,
        ];
    }
}
