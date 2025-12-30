<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'filename' => fake()->uuid().'.jpg',
            'path' => 'test/'.fake()->uuid().'.jpg',
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(1024, 5120 * 1024), // 1KB to 5MB
            'width' => fake()->numberBetween(400, 2000),
            'height' => fake()->numberBetween(300, 1500),
            'alt_text' => fake()->optional()->sentence(),
            'order' => 0,
        ];
    }
}
