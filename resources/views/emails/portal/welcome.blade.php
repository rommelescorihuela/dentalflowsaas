@component('mail::message')
# Hola {{ $patient->name }},

Bienvenido a tu portal de paciente. Desde aquí podrás ver tus citas, presupuestos y recetas.

@component('mail::button', ['url' => $portalUrl])
Acceder a mi portal
@endcomponent

Si el botón no funciona, copia y pega este enlace en tu navegador:

{{ $portalUrl }}

Gracias por confiar en nosotros.

{{ __('emails.common.greetings') }},<br>
{{ __('emails.common.team') }}
@endcomponent
