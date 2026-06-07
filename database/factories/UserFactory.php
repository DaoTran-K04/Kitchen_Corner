<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        // Danh sách bio của bạn
        $bios = [
            'Một người đam mê nấu ăn cuồng nhiệt.',
            'Thích sáng tạo trong góc bếp.',
            'Xin chào, tôi là một người yêu ẩm thực.',
            'Đang tìm kiếm những công thức món ngon mỗi ngày.',
            'Tâm hồn tôi thuộc về những gian bếp ấm áp.',
            'Yêu thích việc khám phá hương vị qua món ăn.',
            'Mỗi món ăn là một câu chuyện tình yêu.',
            'Tôi sống để ăn ngon và nấu ăn để sống rực rỡ.',
            'Chưa thiết lập tiểu sử.',
        ];

        return [
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('123456789'),
            'avatar' => 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($name) . '&background=random&color=fff',
            'bio' => fake()->randomElement($bios),
            'role' => 'user',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'bio' => 'Quản trị viên hệ thống Góc Bếp.',
            'role' => 'admin',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
