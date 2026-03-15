<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
// Tránh sự kiện model khi seeding
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => 'admin',
            'bio' => 'Quản trị viên hệ thống Góc Sách',
            'email_verified_at' => now(),
            'is_active' => true
        ]);

        // Users
        $tester = User::create([
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => 'user',
            'bio' => 'Kiểm thử viên hệ thống Góc Sách',
            'email_verified_at' => now(),
            'is_active' => true
        ]);
        $users = User::factory(60)->create();

        // Danh mục
        $categories = Category::factory()->count(20)->create();

        // 5 cuốn sách Best Seller
        $hotBooks = Book::factory(5)->create([
            'view_count' => fn() => rand(1000, 20000),
            'avg_rating' => fn() => rand(10, 50) / 10,
        ]);

        // Sách mẫu
        $normalBooks = Book::factory(30)->create();
        $allBooks = $hotBooks->merge($normalBooks);

        // Tạo 5 bài viết đang ở trạng thái pending --> Test admin
        Post::factory(5)->create([
            'status' => 'pending',
            'user_id' => $users->random()->id,
            'book_id' => $allBooks->random()->id,
        ]);

        foreach ($hotBooks as $book) {
            Post::factory(3)->create([ // Mỗi sách hot có 3 bài review
                'book_id' => $book->id,
                'status' => 'published',
                'view_count' => rand(1000, 5000),
                'user_id' => $users->random()->id
            ])->each(function ($post) use ($users) {
                // Tự động fake like/comment nhiều cho bài hot
                $this->fakeInteraction($post, $users, 15, 50); // 15-50 like
            });
        }

        // Bài viết không gắn sách
        Post::factory(10)->create([
            'book_id' => null,
            'status' => 'published'
        ]);

        echo "Hoàn tất!";
    }

    // Hàm phụ trợ: Fake like và comment cho bài viết
    private function fakeInteraction($post, $users, $min, $max)
    {
        // Fake Like
        $randomUsers = $users->random(rand($min, $max));
        foreach ($randomUsers as $user) {
            Like::firstOrCreate(['user_id' => $user->id, 'post_id' => $post->id]);
        }

        // Fake Comment
        $commentCount = rand(2, 10);
        for ($i = 0; $i < $commentCount; $i++) {
            Comment::factory()->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id
            ]);
        }
    }
}
