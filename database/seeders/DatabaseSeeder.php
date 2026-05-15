<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Thêm Seeders tĩnh
        $this->call([
            ActivityTitleSeeder::class,
        ]);

        // Admin
        $admin = User::create([
            'name' => 'Admin Góc Bếp',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => 'admin',
            'bio' => 'Bếp trưởng quản trị hệ thống Góc Bếp',
            'email_verified_at' => now(),
            'is_active' => true
        ]);

        // Users
        $tester = User::create([
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => 'user',
            'bio' => 'Người thử nghiệm món ăn',
            'email_verified_at' => now(),
            'is_active' => true
        ]);
        $users = User::factory(30)->create();

        // Danh mục
        $categories = Category::factory()->count(10)->create();

        // 5 Công thức Hot
        $hotRecipes = Recipe::factory(5)->create([
            'view_count' => fn() => rand(5000, 20000),
            'is_featured' => true,
        ]);

        // Công thức bình thường
        $normalRecipes = Recipe::factory(20)->create();
        $allRecipes = $hotRecipes->merge($normalRecipes);

        // Tạo tương tác (Like, Comment) cho Công thức hot
        foreach ($hotRecipes as $recipe) {
            $this->fakeInteraction($recipe, $users, 15, 30); // 15-30 like
        }

        // Tạo tương tác một phần cho công thức bình thường
        foreach ($normalRecipes->random(10) as $recipe) {
            $this->fakeInteraction($recipe, $users, 1, 10);
        }

        echo "Hoàn tất Seeding CSDL Góc Bếp!";
    }

    // Hàm phụ trợ: Fake like và comment cho công thức
    private function fakeInteraction($recipe, $users, $min, $max)
    {
        // Fake Like
        $randomUsers = $users->random(rand($min, $max));
        foreach ($randomUsers as $user) {
            Like::firstOrCreate(['user_id' => $user->id, 'recipe_id' => $recipe->id]);
        }

        // Fake Comment
        $commentCount = rand(2, 5);
        for ($i = 0; $i < $commentCount; $i++) {
            Comment::factory()->create([
                'recipe_id' => $recipe->id,
                'user_id' => $users->random()->id
            ]);
        }
    }
}
