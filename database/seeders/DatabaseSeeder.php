<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Discussion;
use App\Models\Like;
use App\Models\Reply;
use App\Models\User;
use App\Models\Watcher;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an admin user
        $admin = User::factory()
            ->withoutTwoFactor()
            ->admin()
            ->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'points' => 1000,
            ]);

        // Create a test user
        $testUser = User::factory()
            ->withoutTwoFactor()
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        // Create 20 regular users
        $users = User::factory()
            ->count(20)
            ->withoutTwoFactor()
            ->create();

        // Add admin and test user to the users collection
        $allUsers = $users->concat([$admin, $testUser]);

        // Create channels
        $channels = Channel::factory()->count(10)->create();

        // Create discussions with replies
        $channels->each(function ($channel) use ($allUsers) {
            // Create 5-10 discussions per channel
            $discussionCount = rand(5, 10);

            for ($i = 0; $i < $discussionCount; $i++) {
                $discussion = Discussion::factory()->create([
                    'channel_id' => $channel->id,
                    'user_id' => $allUsers->random()->id,
                ]);

                // Create 3-15 replies per discussion
                $replyCount = rand(3, 15);
                $replies = collect();

                for ($j = 0; $j < $replyCount; $j++) {
                    $reply = Reply::factory()->create([
                        'discussion_id' => $discussion->id,
                        'user_id' => $allUsers->random()->id,
                    ]);

                    $replies->push($reply);
                }

                // Mark one random reply as best answer (30% chance)
                if ($replies->isNotEmpty() && rand(1, 100) <= 30) {
                    $bestReply = $replies->random();
                    $bestReply->update(['best_answer' => true]);
                }

                // Add likes to replies
                $replies->each(function ($reply) use ($allUsers) {
                    // Each reply gets 0-8 likes from random users
                    $likeCount = rand(0, 8);
                    $likingUsers = $allUsers->random(min($likeCount, $allUsers->count()));

                    foreach ($likingUsers as $user) {
                        try {
                            Like::factory()->create([
                                'reply_id' => $reply->id,
                                'user_id' => $user->id,
                            ]);
                        } catch (\Exception $e) {
                            // Skip if duplicate (user already liked this reply)
                            continue;
                        }
                    }
                });

                // Add watchers to discussions
                // Each discussion gets 1-5 watchers from random users
                $watcherCount = rand(1, 5);
                $watchingUsers = $allUsers->random(min($watcherCount, $allUsers->count()));

                foreach ($watchingUsers as $user) {
                    try {
                        Watcher::factory()->create([
                            'discussion_id' => $discussion->id,
                            'user_id' => $user->id,
                        ]);
                    } catch (\Exception $e) {
                        // Skip if duplicate (user already watching this discussion)
                        continue;
                    }
                }
            }
        });

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Test User: test@example.com / password');
    }
}
