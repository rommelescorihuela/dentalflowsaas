<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('terms.title') }} - DentalFlow</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 2rem; color: #333; }
        h1 { color: #1e3a8a; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; }
        h2 { color: #2563eb; margin-top: 2rem; }
        p { margin-bottom: 1rem; }
        ul { margin-bottom: 1rem; }
        a { color: #2563eb; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>{{ __('terms.title') }}</h1>
    <p><strong>{{ __('terms.last_updated') }}:</strong> 28 de abril de 2026</p>

    <h2>{{ __('terms.sections.acceptance.title') }}</h2>
    <p>{{ __('terms.sections.acceptance.content') }}</p>

    <h2>{{ __('terms.sections.description.title') }}</h2>
    <p>{{ __('terms.sections.description.content') }}</p>

    <h2>{{ __('terms.sections.accounts.title') }}</h2>
    <ul>
        @foreach(__('terms.sections.accounts.items') as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>

    <h2>{{ __('terms.sections.acceptable_use.title') }}</h2>
    <p>{{ __('terms.sections.acceptable_use.intro') }}</p>
    <ul>
        @foreach(__('terms.sections.acceptable_use.items') as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>

    <h2>{{ __('terms.sections.intellectual_property.title') }}</h2>
    <p>{{ __('terms.sections.intellectual_property.content') }}</p>

    <h2>{{ __('terms.sections.patient_data.title') }}</h2>
    <p>{{ __('terms.sections.patient_data.content') }}</p>

    <h2>{{ __('terms.sections.liability.title') }}</h2>
    <p>{{ __('terms.sections.liability.content') }}</p>

    <h2>{{ __('terms.sections.termination.title') }}</h2>
    <p>{{ __('terms.sections.termination.content') }}</p>

    <h2>{{ __('terms.sections.changes.title') }}</h2>
    <p>{{ __('terms.sections.changes.content') }}</p>

    <h2>{{ __('terms.sections.contact.title') }}</h2>
    <p>{{ __('terms.sections.contact.content') }} <a href="mailto:legal@dentalflow.com">legal@dentalflow.com</a></p>

    <p><a href="/">{{ __('terms.back_to_home') }}</a></p>
</body>
</html>
