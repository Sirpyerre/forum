<?php

namespace Database\Factories;

use App\Models\Discussion;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reply>
 */
class ReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'discussion_id' => Discussion::factory(),
            'content' => fake()->paragraphs(rand(1, 3), true),
            'best_answer' => false,
        ];
    }

    /**
     * Indicate that the reply is a best answer.
     */
    public function bestAnswer(): static
    {
        return $this->state(fn (array $attributes) => [
            'best_answer' => true,
        ]);
    }
}
