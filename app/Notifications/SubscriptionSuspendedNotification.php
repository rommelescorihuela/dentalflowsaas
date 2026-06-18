<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionSuspendedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu suscripción ha sido suspendida')
            ->line('Tu acceso a DentalFlow ha sido suspendido por falta de pago.')
            ->line('Tus datos están preservados. Para reactivar tu acceso, envía tu comprobante de pago.')
            ->action('Subir comprobante', url('/app/billing'))
            ->line('Una vez aprobado tu pago, tu acceso será restituido inmediatamente.');
    }
}
