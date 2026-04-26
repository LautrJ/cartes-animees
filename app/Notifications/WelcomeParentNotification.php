<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
            ->subject('Bienvenue sur Cartes Animées !')
            ->greeting("Bonjour {$this->firstName} !")
            ->line('Nous sommes ravis de vous accueillir sur Cartes Animées, l\'application éducative pour les enfants malentendants.')
            ->line('Vous pouvez dès maintenant créer le profil de votre enfant et découvrir nos séries d\'animations gratuites.')
            ->action('Accéder à l\'application', config('app.frontend_url'))
            ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.');
    }
}
