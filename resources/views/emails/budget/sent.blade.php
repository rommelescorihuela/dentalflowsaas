@component('mail::message')
# Hola {{ $budget->patient->name }}

Tu presupuesto **#{{ $budget->id }}** está listo para revisión.

## Detalle del Presupuesto
- **Total:** ${{ number_format($budget->total, 0, ',', '.') }}
- **Clínica:** {{ $budget->clinic->name ?? 'DentalFlow' }}
- **Válido hasta:** {{ $budget->expires_at?->format('d/m/Y') ?? 'Sin fecha de expiración' }}

@component('mail::button', ['url' => $actionUrl ?? url('/portal/' . $budget->patient->id)])
Ver Presupuesto
@endcomponent

Si tienes alguna duda, no dudes en contactarnos.

Saludos,<br>
El equipo de DentalFlow
@endcomponent
