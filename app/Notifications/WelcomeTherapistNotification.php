<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeTherapistNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly string $firstName,
        public readonly string $invitationCode,
        public readonly string $resetUrl,
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
            ->subject(__('notifications.welcome_therapist.subject'))
            ->greeting(__('notifications.welcome_therapist.greeting', ['first_name' => $this->firstName]))
            ->line(__('notifications.welcome_therapist.line_1'))
            ->line(__('notifications.welcome_therapist.line_2'))
            ->line(__('notifications.welcome_therapist.line_3', ['invitation_code' => $this->invitationCode]))
            ->action(__('notifications.welcome_therapist.action'), $this->resetUrl)
            ->line(__('notifications.welcome_therapist.line_4'));
    }
}
