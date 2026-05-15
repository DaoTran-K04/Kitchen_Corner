<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'recipe_id' => \App\Models\Recipe::factory(),
            'content' => $this->faker->sentence(),
            // Mặc định: Comment gốc
            'parent_id' => null,
        ];
    }

    public function reply()
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => \App\Models\Comment::inRandomOrder()->first()?->id,
            ];
        });
    }
}
