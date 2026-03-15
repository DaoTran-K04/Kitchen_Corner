<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6); // Tạo tiêu đề bài viết

        // Random 80% là bài Review sách, 20% là bài tản mạn không gắn sách
        $hasBook = fake()->boolean(80);
        $book = $hasBook ? (Book::query()->inRandomOrder()->first() ?? Book::factory()) : null;

        // Trạng thái bài viết
        $status = fake()->randomElement(['draft', 'pending', 'published', 'rejected', 'hidden']);

        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'book_id' => $hasBook ? $book->id : null,

            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5), // Thêm random để tránh trùng slug
            'thumbnail' => 'https://placehold.co/800x400?text=' . urlencode('Thumbnail'),
            'excerpt' => fake()->text(200), // Đoạn mô tả ngắn
            'content' => fake()->paragraphs(5, true), // Nội dung bài viết dài

            // Nếu có sách thì mới có rating
            'rating' => $hasBook ? fake()->randomFloat(1, 1, 5) : null,

            'status' => $status,
            'published_at' => $status === 'published' ? fake()->dateTimeBetween('-1 year', 'now') : null,

            'view_count' => fake()->numberBetween(0, 5000),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
