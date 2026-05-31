<?php

namespace App\Notifications;

use App\Models\Child;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class DiscountAppliedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Child $child,
        public readonly float $discountAmount,
        public readonly float $newPrice,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.discount_applied.subject'))
            ->greeting(__('notifications.discount_applied.greeting'))
            ->line(__('notifications.discount_applied.line_1', [
                'child_first_name' => $this->child->first_name,
                'discount_amount'  => $this->discountAmount,
            ]))
            ->line(__('notifications.discount_applied.line_2', [
                'new_price' => number_format($this->newPrice, 2, ',', ' '),
            ]))
            ->action(__('notifications.discount_applied.action'), config('app.frontend_url'))
            ->salutation(new HtmlString(__('notifications.discount_applied.salutation')));
    }
}
