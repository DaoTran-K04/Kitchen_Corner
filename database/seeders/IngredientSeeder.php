<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use Illuminate\Support\Str;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ingredients = [
            // [Tên, Đơn vị, Calo, Protein, Carbs, Fat] (giá trị xấp xỉ trên mỗi đơn vị)
            ['Thịt bò', '100g', 250, 26, 0, 15],
            ['Thịt lợn (nạc)', '100g', 242, 27, 0, 14],
            ['Ức gà', '100g', 165, 31, 0, 3.6],
            ['Cá hồi', '100g', 208, 20, 0, 13],
            ['Trứng gà', 'quả', 70, 6, 0.6, 5],
            ['Đậu phụ', '100g', 76, 8, 1.9, 4.8],
            ['Gạo tẻ', '100g', 130, 2.7, 28, 0.3],
            ['Bún tươi', '100g', 110, 1.7, 25, 0],
            ['Bánh mì', '100g', 265, 9, 49, 3.2],
            ['Sữa tươi', '100ml', 62, 3.3, 4.8, 3.3],
            ['Khoai tây', '100g', 77, 2, 17, 0.1],
            ['Súp lơ xanh', '100g', 34, 2.8, 7, 0.4],
            ['Cà rốt', '100g', 41, 0.9, 10, 0.2],
            ['Cà chua', '100g', 18, 0.9, 3.9, 0.2],
            ['Hành tây', '100g', 40, 1.1, 9, 0.1],
            ['Tỏi', '10g', 15, 0.6, 3.3, 0.1],
            ['Ớt tươi', '10g', 4, 0.2, 0.9, 0],
            ['Dầu ăn', '10ml', 88, 0, 0, 10],
            ['Nước mắm', '10ml', 6, 1.3, 0, 0],
            ['Đường trắng', '10g', 39, 0, 10, 0],
            ['Muối', 'g', 0, 0, 0, 0],
            ['Hạt tiêu', 'g', 2, 0.1, 0.6, 0],
            ['Rau muống', '100g', 19, 2, 3.1, 0.3],
            ['Cải thìa', '100g', 13, 1.5, 2.2, 0.2],
            ['Nấm hương', '100g', 34, 2.2, 7, 0.5],
            ['Tôm tươi', '100g', 99, 24, 0.2, 0.3],
        ];

        foreach ($ingredients as $ing) {
            Ingredient::firstOrCreate(
                ['name' => $ing[0]],
                [
                    'slug' => Str::slug($ing[0]),
                    'unit' => $ing[1],
                    'calories_per_unit' => $ing[2],
                    'protein_per_unit' => $ing[3],
                    'carbs_per_unit' => $ing[4],
                    'fat_per_unit' => $ing[5],
                    'icon' => 'fas fa-leaf', // Mặc định
                ]
            );
        }
    }
}
