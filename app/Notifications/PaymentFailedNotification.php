<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification
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
            ->subject(__('notifications.payment_failed.subject'))
            ->greeting(__('notifications.payment_failed.greeting'))
            ->error()
            ->line(__('notifications.payment_failed.line_1', ['child_first_name' => $this->childFirstName]))
            ->line(__('notifications.payment_failed.line_2'))
            ->action(__('notifications.payment_failed.action'), config('app.frontend_url').'/subscription')
            ->line(__('notifications.payment_failed.line_3'));
    }
}
