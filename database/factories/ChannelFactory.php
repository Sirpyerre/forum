<?php

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Channel>
 */
class ChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->randomElement([
            'General Discussion',
            'Laravel',
            'PHP',
            'JavaScript',
            'Vue.js',
            'React',
            'Database',
            'DevOps',
            'Testing',
            'Security',
            'Performance',
            'UI/UX Design',
            'Mobile Development',
            'API Development',
            'Career & Jobs',
        ]);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->sentence(12),
        ];
    }
}
