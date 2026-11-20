<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DentalFlow') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="landing min-h-screen bg-sand-50">
    <div class="mx-auto flex min-h-screen w-full max-w-md flex-col justify-center px-5 py-12">
        <a href="/" class="mb-8 flex items-center gap-2.5" aria-label="DentalFlow, inicio">
            <svg width="30" height="34" viewBox="0 0 48 54" aria-hidden="true" focusable="false">
                <g transform="rotate(-6 21 25)">
                    <rect x="2" y="4" width="38" height="42" rx="8" fill="#1f9e8f"/>
                    <rect x="7" y="11" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.92"/>
                    <rect x="22" y="11" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.92"/>
                    <rect x="7" y="28" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.72"/>
                    <rect x="22" y="28" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.72"/>
                </g>
            </svg>
            <span class="text-[1.1875rem] font-extrabold tracking-[-0.025em] text-sand-900">Dental<span class="text-primary-500">Flow</span></span>
        </a>

        {{ $slot }}

        <p class="mt-8 text-center text-[0.75rem] text-sand-600">
            <a href="{{ url('/') }}" class="underline underline-offset-4 hover:text-sand-900">Volver a la página principal</a>
        </p>
    </div>
</body>

</html>
