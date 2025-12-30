<?php

namespace App\Notifications;

use App\Models\Discussion;
use App\Models\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReplyNotification extends Notification implements ShouldQueue
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
            ->subject("New reply in: {$this->discussion->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$this->reply->user->name} replied to a discussion you're watching:")
            ->line("**{$this->discussion->title}**")
            ->line('"'.\Str::limit(strip_tags(markdown($this->reply->content)), 100).'"')
            ->action('View Reply', route('discussions.show', $this->discussion))
            ->line('You are receiving this email because you are watching this discussion.');
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
            'reply_author' => $this->reply->user->name,
        ];
    }
}
