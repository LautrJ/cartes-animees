<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeParentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly string $firstName,
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
            ->subject(__('notifications.welcome_parent.subject'))
            ->greeting(__('notifications.welcome_parent.greeting', ['first_name' => $this->firstName]))
            ->line(__('notifications.welcome_parent.line_1'))
            ->line(__('notifications.welcome_parent.line_2'))
            ->action(__('notifications.welcome_parent.action'), config('app.frontend_url'))
            ->line(__('notifications.welcome_parent.line_3'));
    }
}
