<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    public function definition(): array
    {
        $recipes = [
            'Bún bò Huế', 'Phở bò tái', 'Bánh mì thịt nướng', 'Cơm tấm sườn bì chả',
            'Canh chua cá lóc', 'Gà kho gừng', 'Thịt kho trứng', 'Bò lúc lắc',
            'Chả giò hải sản', 'Bánh xèo miền Nam', 'Mì Quảng', 'Bún thịt nướng',
            'Cá kho tộ', 'Sườn xào chua ngọt', 'Rau muống xào tỏi',
            'Lẩu hải sản', 'Bạch tuộc xào sả ớt', 'Gỏi cuốn tôm thịt',
            'Chè đậu xanh nước cốt dừa', 'Bánh flan caramel',
            'Sinh tố bơ', 'Smoothie xoài', 'Trà sữa trân châu tự làm',
            'Cơm chiên dương châu', 'Mì xào hải sản', 'Hủ tiếu Nam Vang',
            'Gà nướng mật ong', 'Vịt quay Bắc Kinh', 'Tôm rang muối',
            'Đậu hũ chiên sả ớt', 'Salad ức gà quinoa', 'Cháo thập cẩm',
            'Súp kem bông cải', 'Sandwich bơ trứng', 'Bánh pancake chuối',
        ];

        $title = $this->faker->unique()->randomElement($recipes) ?? $this->faker->sentence(3);

        return [
            'user_id'         => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'category_id'     => Category::query()->inRandomOrder()->value('id'),
            'title'           => $title,
            'slug'            => Str::slug($title) . '-' . Str::random(4),
            'description'     => $this->faker->paragraph(2),
            'cooking_time'    => $this->faker->randomElement([10, 15, 20, 30, 45, 60, 90, 120]),
            'difficulty'      => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'total_calories'  => $this->faker->numberBetween(100, 800),
            'total_protein'   => $this->faker->numberBetween(5, 50),
            'total_carbs'     => $this->faker->numberBetween(10, 100),
            'total_fat'       => $this->faker->numberBetween(3, 40),
            'image'           => 'https://placehold.co/800x600?text=' . urlencode(Str::limit($title, 20)),
            'view_count'      => $this->faker->numberBetween(0, 5000),
            'is_featured'     => $this->faker->boolean(15),
            'status'          => $this->faker->randomElement(['published', 'published', 'published', 'draft']),
        ];
    }
}
