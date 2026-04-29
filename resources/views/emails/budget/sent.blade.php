@component('mail::message')
# {{ __('emails.budget.sent.greeting', ['name' => $budget->patient->name]) }}

{{ __('emails.budget.sent.intro', ['id' => $budget->id]) }}

## {{ __('emails.budget.sent.detail_title') }}
- **{{ __('emails.budget.sent.total') }}:** ${{ number_format($budget->total, 0, ',', '.') }}
- **{{ __('emails.budget.sent.clinic') }}:** {{ $budget->clinic->name ?? 'DentalFlow' }}
- **{{ __('emails.budget.sent.valid_until') }}:** {{ $budget->expires_at?->format('d/m/Y') ?? __('emails.budget.sent.no_expiry') }}

@component('mail::button', ['url' => $actionUrl ?? url('/portal/' . $budget->patient->id)])
{{ __('emails.budget.sent.button') }}
@endcomponent

{{ __('emails.budget.sent.outro') }}

{{ __('emails.common.greetings') }},<br>
{{ __('emails.common.team') }}
@endcomponent
