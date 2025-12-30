<?php

namespace Database\Seeders;

use App\Models\Discussion;
use App\Models\Like;
use App\Models\Reply;
use App\Models\User;
use App\Models\Watcher;
use Illuminate\Database\Seeder;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $discussions = Discussion::all();

        $replies = [
            'Great news! The pipe operator is a game-changer for functional programming in PHP.',
            "I've been testing PHP 8.5 in production and the performance improvements are noticeable!",
            "This is concerning. We're still on Laminas MVC. What's the migration path?",
            "I recommend checking out Mezzio. The migration isn't too difficult if you follow the docs.",
            "Laravel Wrapped is such a cool feature! Just checked mine and I'm impressed.",
            'The AsUri cast is incredibly useful for API development. Saved me tons of boilerplate code.',
            'Symfony 8.0 dropping old deprecations is exactly what the framework needed. Clean slate!',
            "Does anyone know if there's a migration guide from Symfony 7 to 8?",
            'Symfony AI looks promising. Has anyone integrated it with OpenAI yet?',
            'I tested the LLM integration and it works seamlessly with GPT-4. Documentation is great!',
            "FrankenPHP's worker mode is incredibly fast. Reduced our response times by 40%.",
            'How does FrankenPHP compare to Swoole in terms of stability?',
            'Those EOL statistics are alarming. Security should be a top priority.',
            'We just finished migrating from PHP 7.4 to 8.3. Highly recommend doing it sooner rather than later.',
            "Laravel Cloud's auto-scaling saved us during Black Friday traffic spikes.",
            'Is Laravel Cloud worth it for small projects or better to stick with traditional hosting?',
            "CodeIgniter 4 is still solid for microservices. Don't sleep on it!",
            "Laravel's dominance makes sense - the ecosystem is unmatched.",
            'IaC adoption for PHP is growing but slowly. We use Terraform and it works great.',
            "Pulumi's support for PHP stacks is getting better. Worth checking out.",
        ];

        $repliesCreated = 0;
        $likesCreated = 0;
        $watchersCreated = 0;

        // Add 2-3 replies to each discussion
        foreach ($discussions as $discussion) {
            $numberOfReplies = rand(2, 3);

            for ($i = 0; $i < $numberOfReplies; $i++) {
                if (! isset($replies[$repliesCreated])) {
                    break;
                }

                // Alternate between users for replies
                $replyUser = $users->random();

                // Don't let users reply to their own discussions
                while ($replyUser->id === $discussion->user_id) {
                    $replyUser = $users->random();
                }

                $reply = Reply::create([
                    'discussion_id' => $discussion->id,
                    'user_id' => $replyUser->id,
                    'content' => $replies[$repliesCreated],
                ]);

                $repliesCreated++;

                // Award points for reply
                $replyUser->increment('points', 3);

                // Add 1-2 likes to some replies
                if (rand(0, 1)) {
                    $likeUser = $users->random();
                    while ($likeUser->id === $replyUser->id) {
                        $likeUser = $users->random();
                    }

                    Like::create([
                        'reply_id' => $reply->id,
                        'user_id' => $likeUser->id,
                    ]);

                    // Award points for like
                    $replyUser->increment('points', 2);
                    $likesCreated++;
                }
            }

            // Mark first reply as best answer in some discussions
            if ($discussion->replies->count() > 0 && rand(0, 1)) {
                $bestReply = $discussion->replies->first();
                $bestReply->update(['best_answer' => true]);

                // Award points for best answer
                $bestReply->user->increment('points', 15);
                $discussion->user->increment('points', 5);
            }

            // Add watchers to some discussions
            if (rand(0, 1)) {
                foreach ($users as $user) {
                    if ($user->id !== $discussion->user_id && rand(0, 1)) {
                        Watcher::create([
                            'discussion_id' => $discussion->id,
                            'user_id' => $user->id,
                        ]);
                        $watchersCreated++;
                    }
                }
            }
        }

        $this->command->info("✓ Created {$repliesCreated} replies");
        $this->command->info("✓ Created {$likesCreated} likes");
        $this->command->info("✓ Created {$watchersCreated} watchers");
        $this->command->info('✓ Marked some replies as best answers');
        $this->command->info('✓ Updated user points');
    }
}
