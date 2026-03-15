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
    protected static ?strings $password;

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
            'Một người đam mê sách cuồng nhiệt.',
            'Thích sách.',
            'Xin chào, tôi là một mọt sách.',
            'Đang tìm kiếm những cuốn sách hay để review.',
            'Tâm hồn tôi thuộc về những trang sách.',
            'Yêu thích việc khám phá tri thức qua sách vở.',
            'Mỗi cuốn sách là một cuộc phiêu lưu mới.',
            'Tôi sống để đọc và đọc để sống.',
            'Chưa thiết lập tiểu sử.',
        ];

        return [
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('123456789'),
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=random&color=fff',
            'bio' => fake()->randomElement($bios),
            'role' => 'user',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'bio' => 'Quản trị viên hệ thống Góc Sách.',
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
