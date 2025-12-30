<?php

namespace App\Notifications;

use App\Models\Discussion;
use App\Models\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BestAnswerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Reply $reply,
        public Discussion $discussion
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your answer was marked as the best answer!')
            ->greeting("Congratulations {$notifiable->name}!")
            ->line("{$this->discussion->user->name} marked your answer as the best answer in:")
            ->line("**{$this->discussion->title}**")
            ->line('You earned 15 points for providing the best answer!')
            ->action('View Discussion', route('discussions.show', $this->discussion))
            ->line('Thank you for helping the community!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reply_id' => $this->reply->id,
            'discussion_id' => $this->discussion->id,
            'discussion_title' => $this->discussion->title,
            'marked_by' => $this->discussion->user->name,
        ];
    }
}
