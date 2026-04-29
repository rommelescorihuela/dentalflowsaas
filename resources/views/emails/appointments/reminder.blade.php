@component('mail::message')
# {{ __('emails.appointments.reminder.greeting', ['name' => $appointment->patient->name]) }}

{{ __('emails.appointments.reminder.intro') }}

## {{ __('emails.appointments.reminder.detail_title') }}
- **{{ __('emails.appointments.reminder.date') }}:** {{ $appointment->start_time->format('d/m/Y') }}
- **{{ __('emails.appointments.reminder.time') }}:** {{ $appointment->start_time->format('H:i') }}
- **{{ __('emails.appointments.reminder.type') }}:** {{ ucfirst($appointment->type) }}
- **{{ __('emails.appointments.reminder.notes') }}:** {{ $appointment->notes ?? __('emails.appointments.reminder.no_notes') }}

@component('mail::button', ['url' => $actionUrl ?? url('/portal/' . $appointment->patient->id)])
{{ __('emails.appointments.reminder.button') }}
@endcomponent

{{ __('emails.appointments.reminder.outro') }}

{{ __('emails.common.greetings') }},<br>
{{ __('emails.common.team') }}
@endcomponent
