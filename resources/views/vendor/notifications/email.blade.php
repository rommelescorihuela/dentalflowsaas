@component('mail::message')
# Restablecer Contraseña

Recibiste este correo porque solicitaste restablecer la contraseña de tu cuenta en **DentalFlow**.

@component('mail::button', ['url' => $actionUrl])
Restablecer Contraseña
@endcomponent

Este enlace expirará en **60 minutos**.

Si no solicitaste este cambio, puedes ignorar este correo de forma segura.

Saludos,<br>
El equipo de DentalFlow
@endcomponent
