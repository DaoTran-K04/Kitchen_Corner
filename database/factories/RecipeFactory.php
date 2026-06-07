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
            'image'           => $this->faker->randomElement([
                'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1473093295043-cdd814d0e601?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1499028344343-cd173ffc68a9?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1432139555190-58524dae6a5a?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1484723091791-0fee59cb0c47?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1490818387583-1b5f2b711614?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1517433670267-08bbd4be890f?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1528629297340-d1d466945dc5?auto=format&fit=crop&w=800&q=80',
            ]),
            'view_count'      => $this->faker->numberBetween(0, 5000),
            'is_featured'     => $this->faker->boolean(15),
            'status'          => $this->faker->randomElement(['published', 'published', 'published', 'draft']),
        ];
    }
}
