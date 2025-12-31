<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Channel;
use App\Models\Discussion;
use App\Models\Image;
use App\Models\Like;
use App\Models\Reply;
use App\Models\User;
use App\Models\Watcher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Peter',
            'email' => 'peter@monoforms.com',
            'email_verified_at' => now(),
            'password' => Hash::make('acc50b0ac3aa056e'),
            'admin' => true,
            'points' => 500,
        ]);

        // Create normal user
        $normalUser = User::create([
            'name' => 'Sir Pyerre',
            'email' => 'sir_pyerre@hotmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('7d840a598b0e984e'),
            'admin' => false,
            'points' => 250,
        ]);

        // Create additional users for interaction
        $users = collect([]);
        $userNames = ['Alice Johnson', 'Bob Smith', 'Carol White', 'David Brown', 'Emma Davis', 'Frank Miller', 'Grace Lee', 'Henry Wilson'];

        foreach ($userNames as $name) {
            $users->push(User::create([
                'name' => $name,
                'email' => Str::slug($name).'@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'admin' => false,
                'points' => rand(50, 300),
            ]));
        }

        // Add admin and normal user to collection
        $allUsers = $users->concat([$admin, $normalUser]);

        // Create channels
        $channelData = [
            ['title' => 'General Discussion', 'slug' => 'general-discussion', 'description' => 'Talk about anything related to PHP and web development'],
            ['title' => 'PHP', 'slug' => 'php', 'description' => 'PHP language discussions and updates'],
            ['title' => 'Cloud', 'slug' => 'cloud', 'description' => 'Cloud infrastructure and deployment'],
            ['title' => 'Performance', 'slug' => 'performance', 'description' => 'Performance optimization and benchmarking'],
            ['title' => 'Security', 'slug' => 'security', 'description' => 'Security best practices and vulnerabilities'],
            ['title' => 'API Development', 'slug' => 'api-development', 'description' => 'Building and consuming APIs'],
            ['title' => 'DevOps', 'slug' => 'devops', 'description' => 'DevOps practices and tools'],
        ];

        $channels = collect();
        foreach ($channelData as $data) {
            $channels->push(Channel::create($data));
        }

        // Discussions data from JSON
        $discussionsData = [
            [
                'title' => 'Laminas MVC enters Retirement Phase',
                'content' => 'The Laminas Project has officially announced that Laminas MVC is retiring. Developers are encouraged to migrate to Mezzio or other PSR-15 middleware-based architectures for future-proofing.',
                'image' => 'https://getlaminas.org/images/logo/laminas-logo.svg',
                'channel' => 'General Discussion',
            ],
            [
                'title' => 'PHP 8.5 Release: New Pipe Operator',
                'content' => 'The official release of PHP 8.5 introduces the highly requested pipe operator (|>), providing a more readable way to chain functions and improve code maintainability.',
                'image' => 'https://www.php.net/images/logos/php-logo.svg',
                'channel' => 'PHP',
            ],
            [
                'title' => 'Laravel 12: Revolutionizing Full-Stack Speed',
                'content' => 'Laravel 12 introduces enhanced AI-driven scaffolding and optimized first-party integration with Laravel Cloud for near-instant deployment cycles.',
                'image' => 'https://laravel.com/img/logomark.min.svg',
                'channel' => 'Cloud',
            ],
            [
                'title' => 'Framework Landscape 2025: Beyond Laravel',
                'content' => 'While Laravel remains the leader, Symfony 8.0 and CodeIgniter 4 are seeing a resurgence in enterprise environments due to their modularity and performance benchmarks.',
                'image' => 'https://www.encodedots.com/blog/wp-content/uploads/2023/04/PHP-Frameworks.jpg',
                'channel' => 'General Discussion',
            ],
            [
                'title' => 'FrankenPHP: The New Standard for PHP Apps',
                'content' => 'FrankenPHP has been adopted as the recommended application server for high-concurrency Laravel apps, significantly reducing memory footprint compared to FPM.',
                'image' => 'https://blog.jetbrains.com/wp-content/uploads/2024/10/frankenphp.png',
                'channel' => 'Performance',
            ],
            [
                'title' => 'Critical Security Audit Results for PHP Core',
                'content' => 'The PHP Foundation released a comprehensive 2025 security audit, patching several low-level vulnerabilities and hardening the Zend Engine against memory exploits.',
                'image' => 'https://www.php.net/images/news/security-audit-2025.png',
                'channel' => 'Security',
            ],
            [
                'title' => 'API Development with Pest 4 and Laravel',
                'content' => 'Pest 4 has introduced native API snapshot testing, allowing developers to ensure contract consistency across microservices with minimal configuration.',
                'image' => 'https://pestphp.com/assets/img/logo.png',
                'channel' => 'API Development',
            ],
            [
                'title' => 'DevOps: PHP on ARM64 Infrastructure',
                'content' => 'A major shift in 2025 shows 60% of new PHP deployments moving to ARM64 architecture on AWS and Google Cloud to optimize cost-to-performance ratios.',
                'image' => 'https://blog.jetbrains.com/wp-content/uploads/2024/10/php-tools-2025.png',
                'channel' => 'DevOps',
            ],
        ];

        // Create discussions
        foreach ($discussionsData as $index => $data) {
            $channel = $channels->firstWhere('title', $data['channel']);
            $author = $index % 2 === 0 ? $admin : $normalUser; // Alternate between admin and normal user

            $discussion = Discussion::create([
                'user_id' => $author->id,
                'channel_id' => $channel->id,
                'title' => $data['title'],
                'content' => $data['content'],
                'slug' => Str::slug($data['title']),
                'views' => rand(10, 500),
            ]);

            // Create image record for the discussion (store URL as path for now)
            if (! empty($data['image'])) {
                Image::create([
                    'imageable_type' => Discussion::class,
                    'imageable_id' => $discussion->id,
                    'filename' => basename($data['image']),
                    'path' => $data['image'], // Store URL
                    'disk' => 'public',
                    'mime_type' => 'image/png',
                    'size' => 0,
                    'order' => 0,
                ]);
            }

            // Create 3-8 replies per discussion
            $replyCount = rand(3, 8);
            $replies = collect();

            $replyTexts = [
                'This is a great point! I\'ve been following this development closely.',
                'I agree with this approach. We should definitely consider migrating.',
                'Interesting perspective. Do you have any benchmarks to share?',
                'Thanks for sharing this information. Very helpful!',
                'I\'m not sure I agree with this. What about backward compatibility?',
                'Has anyone tried this in production yet?',
                'This could be a game-changer for our project!',
                'Great explanation. Looking forward to seeing this in action.',
                'I have some concerns about performance. Has this been tested at scale?',
                'Excellent write-up! Can you provide more details on implementation?',
                'This aligns perfectly with what we\'re trying to achieve.',
                'I\'ve been using this for a few weeks now and it\'s fantastic!',
                'Does this work with existing setups or require a complete rewrite?',
                'The community has been waiting for this for years!',
                'I\'m curious about the security implications of this change.',
            ];

            for ($i = 0; $i < $replyCount; $i++) {
                $replyAuthor = $allUsers->random();
                $reply = Reply::create([
                    'discussion_id' => $discussion->id,
                    'user_id' => $replyAuthor->id,
                    'content' => $replyTexts[array_rand($replyTexts)],
                    'best_answer' => false,
                ]);

                $replies->push($reply);

                // Award points for reply
                $replyAuthor->increment('points', 3);
            }

            // Mark one reply as best answer (50% chance)
            if ($replies->isNotEmpty() && rand(0, 1)) {
                $bestReply = $replies->random();
                $bestReply->update(['best_answer' => true]);

                // Award points for best answer
                $bestReply->user->increment('points', 15);
                $discussion->user->increment('points', 5);
            }

            // Add likes to replies (random distribution)
            $replies->each(function ($reply) use ($allUsers) {
                $likeCount = rand(0, 5);
                $likingUsers = $allUsers->random(min($likeCount, $allUsers->count()));

                foreach ($likingUsers as $user) {
                    try {
                        Like::create([
                            'reply_id' => $reply->id,
                            'user_id' => $user->id,
                        ]);

                        // Award points for receiving like
                        $reply->user->increment('points', 2);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            });

            // Add watchers (2-4 per discussion)
            $watcherCount = rand(2, 4);
            $watchingUsers = $allUsers->random(min($watcherCount, $allUsers->count()));

            foreach ($watchingUsers as $user) {
                try {
                    Watcher::create([
                        'discussion_id' => $discussion->id,
                        'user_id' => $user->id,
                    ]);
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Award points to discussion author
            $discussion->user->increment('points', 5);
        }

        // Award badges based on points
        $this->awardBadges($allUsers);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: peter@monoforms.com / acc50b0ac3aa056e');
        $this->command->info('User: sir_pyerre@hotmail.com / 7d840a598b0e984e');
        $this->command->info('Other users: *@example.com / password');
    }

    private function awardBadges($users)
    {
        // Ensure badges exist
        $badges = [
            ['name' => 'Welcome', 'slug' => 'welcome', 'description' => 'Joined the community', 'points_required' => 0],
            ['name' => 'Contributor', 'slug' => 'contributor', 'description' => 'Reached 100 points', 'points_required' => 100],
            ['name' => 'Expert', 'slug' => 'expert', 'description' => 'Reached 250 points', 'points_required' => 250],
            ['name' => 'Master', 'slug' => 'master', 'description' => 'Reached 500 points', 'points_required' => 500],
            ['name' => 'Legend', 'slug' => 'legend', 'description' => 'Reached 1000 points', 'points_required' => 1000],
        ];

        foreach ($badges as $badgeData) {
            Badge::firstOrCreate(
                ['slug' => $badgeData['slug']],
                $badgeData
            );
        }

        // Award badges to users based on their points
        foreach ($users as $user) {
            $user->refresh(); // Refresh to get updated points

            $earnedBadges = Badge::where('points_required', '<=', $user->points)
                ->get();

            foreach ($earnedBadges as $badge) {
                $user->badges()->syncWithoutDetaching([$badge->id]);
            }
        }
    }
}
