@php
    $getColor = fn($code) => $code && isset($colors[$code]) ? $colors[$code] : '#94a3b8';
    $surfaces = $surfaces ?? [];
    $cx = $x + 20;
    $cy = 35;
@endphp

<g transform="translate({{ $x }}, 5)">
    {{-- Número de diente --}}
    <text x="20" y="8" text-anchor="middle" font-size="8" font-weight="bold" fill="#4b5563">{{ $tooth }}</text>

    {{-- Root (raíz) --}}
    <line x1="20" y1="48" x2="20" y2="58"
          stroke="{{ isset($surfaces['root']) ? $getColor($surfaces['root']) : '#94a3b8' }}"
          stroke-width="3" stroke-linecap="round" />

    {{-- Circulo base del diente --}}
    <circle cx="20" cy="35" r="14" fill="white" stroke="#9ca3af" stroke-width="1" />

    {{-- Top (superior) --}}
    @if (isset($surfaces['top']))
        <path d="M 8 27 A 14 14 0 0 1 32 27 L 32 30 L 8 30 Z" fill="{{ $getColor($surfaces['top']) }}" stroke="#9ca3af" stroke-width="0.5" />
    @endif

    {{-- Bottom (inferior) --}}
    @if (isset($surfaces['bottom']))
        <path d="M 8 43 A 14 14 0 0 0 32 43 L 32 40 L 8 40 Z" fill="{{ $getColor($surfaces['bottom']) }}" stroke="#9ca3af" stroke-width="0.5" />
    @endif

    {{-- Left (izquierda) --}}
    @if (isset($surfaces['left']))
        <path d="M 8 27 A 14 14 0 0 0 8 43 L 11 40 L 11 30 Z" fill="{{ $getColor($surfaces['left']) }}" stroke="#9ca3af" stroke-width="0.5" />
    @endif

    {{-- Right (derecha) --}}
    @if (isset($surfaces['right']))
        <path d="M 32 27 A 14 14 0 0 1 32 43 L 29 40 L 29 30 Z" fill="{{ $getColor($surfaces['right']) }}" stroke="#9ca3af" stroke-width="0.5" />
    @endif

    {{-- Center (centro) --}}
    @if (isset($surfaces['center']))
        <rect x="11" y="30" width="18" height="10" fill="{{ $getColor($surfaces['center']) }}" stroke="#9ca3af" stroke-width="0.5" />
    @endif

    {{-- Si no hay superficies registradas, el diente está sano --}}
    @if (empty($surfaces))
        <circle cx="20" cy="35" r="14" fill="#ffffff" stroke="#9ca3af" stroke-width="1" />
    @endif

    {{-- Missing: X sobre el diente --}}
    @if (isset($surfaces['center']) && $surfaces['center'] === 'missing')
        <line x1="10" y1="25" x2="30" y2="45" stroke="#1f2937" stroke-width="2" />
        <line x1="30" y1="25" x2="10" y2="45" stroke="#1f2937" stroke-width="2" />
    @endif
</g>
