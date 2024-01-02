<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'status' => Post::OPEN,
            'title' => $this->faker->realText(20),
            'body' => $this->faker->realText(200),
        ];
    }

    // Postエンティティのstatus属性にランダムな値を設定するstate定義
    // Post::OPENの確率を高めるために、配列に複数回含めている。
    public function random()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => $this->faker->randomElement([Post::OPEN, Post::OPEN, Post::OPEN ,Post::OPEN, Post::CLOSED]),
            ];
        });
    }
}
