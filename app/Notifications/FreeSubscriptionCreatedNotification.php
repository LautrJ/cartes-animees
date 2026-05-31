<?php

namespace App\Notifications;

use App\Models\Child;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class FreeSubscriptionCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Child $child,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.free_subscription_created.subject'))
            ->greeting(__('notifications.free_subscription_created.greeting'))
            ->line(__('notifications.free_subscription_created.line_1', [
                'child_first_name' => $this->child->first_name,
            ]))
            ->line(__('notifications.free_subscription_created.line_2'))
            ->action(__('notifications.free_subscription_created.action'), config('app.frontend_url'))
            ->salutation(new HtmlString(__('notifications.free_subscription_created.salutation')));
    }
}
