<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialExpiredNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu período de prueba ha terminado')
            ->line('Tu período de prueba de DentalFlow ha finalizado.')
            ->line('Tienes 7 días de gracia para enviar tu comprobante de pago.')
            ->action('Subir comprobante', url('/app/billing'))
            ->line('Después del período de gracia, tu acceso será suspendido.');
    }
}
