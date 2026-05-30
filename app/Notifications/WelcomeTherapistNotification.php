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
            ->subject('Bienvenue sur Cartes Animées !')
            ->greeting("Bonjour {$this->firstName} !")
            ->line('Votre compte orthophoniste a bien été créé sur Cartes Animées.')
            ->line('Vous pouvez dès maintenant gérer vos patients, créer du contenu et suivre leur progression.')
            ->line("Votre code d'invitation à partager à vos patients : **{$this->invitationCode}**")
            ->action('Définir mon mot de passe', $this->resetUrl)
            ->line('Ce lien est valable 60 minutes.');
    }
}
