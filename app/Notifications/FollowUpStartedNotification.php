<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowUpStartedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly string $childFirstName,
        public readonly string $therapistFirstName,
        public readonly string $therapistLastName,
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
            ->subject(__('notifications.follow_up_started.subject', ['child_first_name' => $this->childFirstName]))
            ->greeting(__('notifications.follow_up_started.greeting'))
            ->line(__('notifications.follow_up_started.line_1', [
                'therapist_first_name' => $this->therapistFirstName,
                'therapist_last_name' => $this->therapistLastName,
                'child_first_name' => $this->childFirstName,
            ]))
            ->line(__('notifications.follow_up_started.line_2'))
            ->action(__('notifications.follow_up_started.action'), config('app.frontend_url'))
            ->line(__('notifications.follow_up_started.line_3'));
    }
}
