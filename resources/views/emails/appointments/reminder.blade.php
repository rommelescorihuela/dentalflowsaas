@component('mail::message')
# Hola {{ $appointment->patient->name }}

Te recordamos que tienes una cita programada en nuestra clínica.

## Detalle de la Cita
- **Fecha:** {{ $appointment->start_time->format('d/m/Y') }}
- **Hora:** {{ $appointment->start_time->format('H:i') }}
- **Tipo:** {{ ucfirst($appointment->type) }}
- **Notas:** {{ $appointment->notes ?? 'Sin notas adicionales' }}

@component('mail::button', ['url' => $actionUrl ?? url('/portal/' . $appointment->patient->id)])
Ver Mis Citas
@endcomponent

Si necesitas reagendar o cancelar, por favor contáctanos con al menos 24 horas de antelación.

Saludos,<br>
El equipo de DentalFlow
@endcomponent
