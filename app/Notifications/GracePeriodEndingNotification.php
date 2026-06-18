<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GracePeriodEndingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu acceso será suspendido en '.$this->daysRemaining.' días')
            ->line('Tu período de gracia está por terminar.')
            ->line('Tienes '.$this->daysRemaining.' días antes de que tu acceso sea suspendido.')
            ->action('Subir comprobante ahora', url('/app/billing'))
            ->line('Evita la suspensión enviando tu pago a tiempo.');
    }
}
