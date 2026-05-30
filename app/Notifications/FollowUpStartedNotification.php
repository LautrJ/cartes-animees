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
            ->subject("Nouveau suivi orthophoniste — {$this->childFirstName}")
            ->greeting('Bonjour !')
            ->line("**{$this->therapistFirstName} {$this->therapistLastName}** assure désormais le suivi de **{$this->childFirstName}** sur Cartes Animées.")
            ->line('Il pourra débloquer des séries adaptées à la progression de votre enfant.')
            ->action('Accéder à l\'application', config('app.frontend_url'))
            ->line('Vous pouvez retrouver les informations de suivi depuis votre espace personnel.');
    }
}
