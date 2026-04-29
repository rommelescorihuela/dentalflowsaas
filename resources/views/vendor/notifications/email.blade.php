@component('mail::message')
# {{ __('emails.password_reset.subject') }}

{{ __('emails.password_reset.intro') }}

@component('mail::button', ['url' => $actionUrl])
{{ __('emails.password_reset.button') }}
@endcomponent

{{ __('emails.password_reset.expiry', ['count' => 60]) }}

{{ __('emails.password_reset.ignore') }}

{{ __('emails.common.greetings') }},<br>
{{ __('emails.common.team') }}
@endcomponent
