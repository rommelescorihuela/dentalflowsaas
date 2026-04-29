<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('privacy.title') }} - DentalFlow</title>
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
    <h1>{{ __('privacy.title') }}</h1>
    <p><strong>{{ __('privacy.last_updated') }}:</strong> 28 de abril de 2026</p>

    <h2>{{ __('privacy.sections.introduction.title') }}</h2>
    <p>{{ __('privacy.sections.introduction.content') }}</p>

    <h2>{{ __('privacy.sections.data_collected.title') }}</h2>
    <ul>
        @foreach(__('privacy.sections.data_collected.items') as $item)
            <li>{!! $item !!}</li>
        @endforeach
    </ul>

    <h2>{{ __('privacy.sections.data_usage.title') }}</h2>
    <p>{{ __('privacy.sections.data_usage.intro') }}</p>
    <ul>
        @foreach(__('privacy.sections.data_usage.items') as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>

    <h2>{{ __('privacy.sections.data_sharing.title') }}</h2>
    <p>{{ __('privacy.sections.data_sharing.intro') }}</p>
    <ul>
        @foreach(__('privacy.sections.data_sharing.items') as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>

    <h2>{{ __('privacy.sections.security.title') }}</h2>
    <p>{{ __('privacy.sections.security.intro') }}</p>
    <ul>
        @foreach(__('privacy.sections.security.items') as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>

    <h2>{{ __('privacy.sections.retention.title') }}</h2>
    <p>{{ __('privacy.sections.retention.content') }}</p>

    <h2>{{ __('privacy.sections.user_rights.title') }}</h2>
    <p>{{ __('privacy.sections.user_rights.intro') }}</p>
    <ul>
        @foreach(__('privacy.sections.user_rights.items') as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>

    <h2>{{ __('privacy.sections.compliance.title') }}</h2>
    <p>{{ __('privacy.sections.compliance.content') }}</p>

    <h2>{{ __('privacy.sections.cookies.title') }}</h2>
    <p>{{ __('privacy.sections.cookies.content') }}</p>

    <h2>{{ __('privacy.sections.contact.title') }}</h2>
    <p>{{ __('privacy.sections.contact.intro') }} <a href="mailto:privacy@dentalflow.com">privacy@dentalflow.com</a></p>

    <p><a href="/">{{ __('privacy.back_to_home') }}</a></p>
</body>
</html>
