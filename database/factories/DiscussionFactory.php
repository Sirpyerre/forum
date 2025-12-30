<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Discussion>
 */
class DiscussionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(rand(4, 8));

        return [
            'user_id' => User::factory(),
            'channel_id' => Channel::factory(),
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'content' => fake()->paragraphs(rand(2, 5), true),
            'views' => fake()->numberBetween(0, 1000),
        ];
    }
}
