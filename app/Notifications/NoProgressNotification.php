<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoProgressNotification extends Notification
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
            ->subject("Pas d'activité récente — {$this->childFirstName}")
            ->greeting('Bonjour !')
            ->line("Nous n'avons pas détecté d'activité de **{$this->childFirstName}** sur Cartes Animées depuis plus d'une semaine.")
            ->line('La régularité est importante pour la progression de votre enfant.')
            ->action('Reprendre les exercices', config('app.frontend_url'))
            ->line('Votre orthophoniste référent peut également débloquer de nouvelles séries pour maintenir la motivation.');
    }
}
