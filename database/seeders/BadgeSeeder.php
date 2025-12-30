<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Newcomer',
                'slug' => 'newcomer',
                'description' => 'Welcome to the community! You created your account.',
                'icon' => '👋',
                'color' => 'gray',
                'points_required' => 0,
            ],
            [
                'name' => 'Contributor',
                'slug' => 'contributor',
                'description' => 'You are actively participating in discussions.',
                'icon' => '💬',
                'color' => 'blue',
                'points_required' => 50,
            ],
            [
                'name' => 'Helper',
                'slug' => 'helper',
                'description' => 'You are helping others by providing answers.',
                'icon' => '🤝',
                'color' => 'green',
                'points_required' => 100,
            ],
            [
                'name' => 'Expert',
                'slug' => 'expert',
                'description' => 'Your expertise is recognized by the community.',
                'icon' => '⭐',
                'color' => 'yellow',
                'points_required' => 250,
            ],
            [
                'name' => 'Guru',
                'slug' => 'guru',
                'description' => 'You are a master of your craft.',
                'icon' => '🏆',
                'color' => 'purple',
                'points_required' => 500,
            ],
            [
                'name' => 'Legend',
                'slug' => 'legend',
                'description' => 'Your contributions are legendary!',
                'icon' => '👑',
                'color' => 'indigo',
                'points_required' => 1000,
            ],
            [
                'name' => 'First Reply',
                'slug' => 'first-reply',
                'description' => 'You posted your first reply.',
                'icon' => '🎯',
                'color' => 'pink',
                'points_required' => 0,
            ],
            [
                'name' => 'First Best Answer',
                'slug' => 'first-best-answer',
                'description' => 'Your answer was marked as the best answer for the first time!',
                'icon' => '✅',
                'color' => 'green',
                'points_required' => 0,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
