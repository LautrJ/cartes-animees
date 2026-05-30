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
            ->subject('Échec de paiement — Cartes Animées')
            ->greeting('Bonjour !')
            ->error()
            ->line("Le renouvellement de l'abonnement de **{$this->childFirstName}** a échoué.")
            ->line('L\'accès aux séries payantes a été suspendu temporairement.')
            ->action('Mettre à jour mes informations de paiement', config('app.frontend_url').'/subscription')
            ->line('L\'accès sera automatiquement restauré après régularisation du paiement.');
    }
}
