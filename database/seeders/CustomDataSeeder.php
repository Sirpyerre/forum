<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users
        $peter = User::create([
            'name' => 'Peter',
            'email' => 'peter@monoforms.com',
            'password' => bcrypt('password'),
            'points' => 150,
            'admin' => true,
        ]);

        $pierre = User::create([
            'name' => 'Sir Pyerre',
            'email' => 'sir_pyerre@hotmail.com',
            'password' => bcrypt('password'),
            'points' => 100,
            'admin' => false,
        ]);

        // Discussions data
        $discussions = [
            [
                'title' => 'PHP 8.5 Official Release',
                'description' => 'The PHP team announced the general availability of PHP 8.5, featuring the new pipe operator (|>), the URI extension, and the #[NoDiscard] attribute to prevent ignored return values.',
                'image' => 'https://www.php.net/images/logos/php-logo.svg',
                'channel' => 'PHP',
            ],
            [
                'title' => 'Laminas MVC Retirement Announcement',
                'description' => 'The Laminas Project has officially announced the retirement of the Laminas MVC framework, encouraging users to migrate to Mezzio or other modern PSR-15 compliant alternatives.',
                'image' => 'https://getlaminas.org/images/logo/laminas-logo.svg',
                'channel' => 'General Discussion',
            ],
            [
                'title' => 'Laravel 12.x and Laravel Wrapped',
                'description' => "Laravel 12 has been released introducing 'Laravel Wrapped' to visualize annual contributions, alongside the new AsUri model cast and contextual binding using PHP 8 attributes.",
                'image' => 'https://laravel.com/img/logomark.min.svg',
                'channel' => 'API Development',
            ],
            [
                'title' => 'Symfony 8.0 and 7.4 Launch',
                'description' => 'Symfony released versions 7.4 (LTS) and 8.0 simultaneously in late 2025. Version 8.0 requires PHP 8.4+ and removes all previous deprecations for a cleaner core.',
                'image' => 'https://symfony.com/logos/symfony_black_03.svg',
                'channel' => 'Performance',
            ],
            [
                'title' => 'The Rise of Symfony AI',
                'description' => "Symfony has tagged its first release of 'Symfony AI' (v0.1.0), a new component designed to integrate large language models (LLMs) directly into the framework ecosystem.",
                'image' => 'https://symfony.com/blog/images/symfony-ai.png',
                'channel' => 'Cloud',
            ],
            [
                'title' => 'FrankenPHP Joins PHP Foundation',
                'description' => 'FrankenPHP is now an official project under the PHP Foundation, offering a high-performance Go-based application server with built-in support for early hints and worker mode.',
                'image' => 'https://blog.jetbrains.com/wp-content/uploads/2024/10/frankenphp.png',
                'channel' => 'Performance',
            ],
            [
                'title' => 'State of PHP 2025: Security Trends',
                'description' => 'The 2025 Landscape Report reveals that while 76% of teams migrated versions last year, nearly 38% are still running End-of-Life (EOL) versions, posing security risks.',
                'image' => 'https://www.php.net/images/news/security-audit-2025.png',
                'channel' => 'Security',
            ],
            [
                'title' => 'Laravel Cloud SOC 2 Compliance',
                'description' => 'Laravel Cloud is now live and SOC 2 Type 1 compliant, providing enterprise-grade infrastructure that scales automatically for high-traffic applications.',
                'image' => 'https://laravel-news.com/images/laravel-news-logo.png',
                'channel' => 'Cloud',
            ],
            [
                'title' => 'Popular Frameworks Shift in 2025',
                'description' => 'While Laravel dominates, frameworks like Symfony, CodeIgniter 4, and Phalcon continue to evolve to meet specific microservices and high-concurrency needs.',
                'image' => 'https://www.encodedots.com/blog/wp-content/uploads/2023/04/PHP-Frameworks.jpg',
                'channel' => 'General Discussion',
            ],
            [
                'title' => 'Infrastructure as Code (IaC) for PHP',
                'description' => 'Emerging trends show increased use of Terraform and Pulumi specifically tailored for PHP stacks, though adoption in smaller teams remains at 0% according to recent community polls.',
                'image' => 'https://blog.jetbrains.com/wp-content/uploads/2024/10/php-tools-2025.png',
                'channel' => 'IaC',
            ],
        ];

        // Create discussions
        foreach ($discussions as $index => $discussionData) {
            // Find or create channel
            $channel = Channel::firstOrCreate(
                ['slug' => Str::slug($discussionData['channel'])],
                [
                    'title' => $discussionData['channel'],
                    'description' => "Discussions about {$discussionData['channel']}",
                ]
            );

            // Alternate between users
            $user = $index % 2 === 0 ? $peter : $pierre;

            // Create discussion
            Discussion::create([
                'user_id' => $user->id,
                'channel_id' => $channel->id,
                'title' => $discussionData['title'],
                'slug' => Str::slug($discussionData['title']),
                'content' => $discussionData['description'],
                'views' => rand(10, 500),
            ]);
        }

        $this->command->info('✓ Created 2 users');
        $this->command->info('✓ Created 10 discussions across multiple channels');
    }
}
