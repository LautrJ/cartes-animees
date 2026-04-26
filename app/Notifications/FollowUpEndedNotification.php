<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowUpEndedNotification extends Notification
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
            ->subject("Fin de suivi orthophoniste — {$this->childFirstName}")
            ->greeting('Bonjour !')
            ->line("Le suivi de **{$this->childFirstName}** par **{$this->therapistFirstName} {$this->therapistLastName}** a pris fin.")
            ->line('L\'abonnement de votre enfant reste actif et son accès aux séries débloquées est maintenu.')
            ->action('Accéder à l\'application', config('app.frontend_url'))
            ->line('Vous pouvez rattacher un nouvel orthophoniste depuis votre espace personnel si besoin.');
    }
}
