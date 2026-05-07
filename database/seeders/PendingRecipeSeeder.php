<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;

class PendingRecipeSeeder extends Seeder
{
    public function run()
    {
        $users = User::whereIn('email', ['tester@gmail.com', 'chef_master@gmail.com', 'megau@gmail.com'])->get();
        if ($users->isEmpty()) {
            $users = User::limit(3)->get();
        }

        $categories = Category::all();

        $pendingRecipes = [
            [
                'title' => 'Bánh Xèo Miền Tây Giòn Rụm',
                'description' => 'Công thức gia truyền giúp vỏ bánh giòn tan, thơm mùi nước cốt dừa và nghệ.',
                'image' => 'https://images.unsplash.com/photo-1589118949245-7d38baf380d6?w=600',
                'cooking_time' => 45,
                'difficulty' => 'medium',
                'status' => 'pending',
                'user_id' => $users[0]->id,
                'category_id' => 4, // Ẩm thực miền Nam
                'total_calories' => 350,
                'is_featured' => false,
            ],
            [
                'title' => 'Lẩu Thả Phan Thiết',
                'description' => 'Món lẩu đặc sản vùng biển với nguyên liệu tươi sống và cách trình bày độc đáo.',
                'image' => 'https://images.unsplash.com/photo-1555126634-323283e090fa?w=600',
                'cooking_time' => 60,
                'difficulty' => 'hard',
                'status' => 'pending',
                'user_id' => $users[1]->id ?? $users[0]->id,
                'category_id' => 10, // Ẩm thực miền Trung
                'total_calories' => 500,
                'is_featured' => true,
            ],
            [
                'title' => 'Gỏi Cuốn Tôm Thịt',
                'description' => 'Món ăn thanh đạm, đầy đủ dinh dưỡng với nước chấm tương đen đặc trưng.',
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600',
                'cooking_time' => 30,
                'difficulty' => 'easy',
                'status' => 'pending',
                'user_id' => $users[2]->id ?? $users[0]->id,
                'category_id' => 3, // Món khai vị
                'total_calories' => 200,
                'is_featured' => false,
            ],
            [
                'title' => 'Sườn Non Kho Tộ',
                'description' => 'Vị mặn ngọt hài hòa, sườn mềm thấm vị, cực kỳ đưa cơm.',
                'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600',
                'cooking_time' => 40,
                'difficulty' => 'medium',
                'status' => 'pending',
                'user_id' => $users[0]->id,
                'category_id' => 8, // Món chính
                'total_calories' => 420,
                'is_featured' => false,
            ]
        ];

        foreach ($pendingRecipes as $data) {
            $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);
            Recipe::create($data);
        }
    }
}
