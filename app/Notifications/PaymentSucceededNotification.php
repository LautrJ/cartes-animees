<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSucceededNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly string $childFirstName,
        public readonly float $amount,
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
            ->subject(__('notifications.payment_succeeded.subject'))
            ->greeting(__('notifications.payment_succeeded.greeting'))
            ->success()
            ->line(__('notifications.payment_succeeded.line_1', ['child_first_name' => $this->childFirstName]))
            ->line(__('notifications.payment_succeeded.line_2', ['amount' => number_format($this->amount, 2, ',', ' ')]))
            ->action(__('notifications.payment_succeeded.action'), config('app.frontend_url'))
            ->line(__('notifications.payment_succeeded.line_3'));
    }
}
