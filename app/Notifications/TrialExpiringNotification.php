<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialExpiringNotification extends Notification
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
            ->subject('Tu período de prueba termina en '.$this->daysRemaining.' días')
            ->line('Tu período de prueba de DentalFlow está por terminar.')
            ->line('Te quedan '.$this->daysRemaining.' días de acceso completo al plan Pro.')
            ->action('Subir comprobante de pago', url('/app/billing'))
            ->line('¡No pierdas acceso a tu gestión clínica!');
    }
}
