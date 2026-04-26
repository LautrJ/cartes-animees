<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        public readonly float  $amount,
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
            ->subject('Paiement confirmé — Cartes Animées')
            ->greeting('Bonjour !')
            ->success()
            ->line("Le paiement de l'abonnement de **{$this->childFirstName}** a bien été effectué.")
            ->line('Montant débité : **' . number_format($this->amount, 2, ',', ' ') . ' €**')
            ->action('Accéder à l\'application', config('app.frontend_url'))
            ->line('Merci pour votre confiance !');
    }
}
