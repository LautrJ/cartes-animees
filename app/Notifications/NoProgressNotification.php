<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoProgressNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly string $childFirstName,
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
            ->subject(__('notifications.no_progress.subject', ['child_first_name' => $this->childFirstName]))
            ->greeting(__('notifications.no_progress.greeting'))
            ->line(__('notifications.no_progress.line_1', ['child_first_name' => $this->childFirstName]))
            ->line(__('notifications.no_progress.line_2'))
            ->action(__('notifications.no_progress.action'), config('app.frontend_url'))
            ->line(__('notifications.no_progress.line_3'));
    }
}
