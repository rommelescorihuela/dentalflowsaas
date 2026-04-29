@php
    $getColor = fn($code) => $code && isset($colors[$code]) ? $colors[$code] : '#94a3b8';
@endphp
<div class="flex flex-col items-center gap-1"
    style="display: flex; flex-direction: column; align-items: center; gap: 0.25rem;">
    <!-- Tooth Number -->
    <span class="text-xs font-bold text-gray-600" style="font-size: 0.75rem; font-weight: 700; color: #4b5563;">
        {{ $number }}
    </span>

    <!-- SVG Container -->
    <div class="relative group cursor-pointer w-full" style="position: relative; cursor: pointer; width: 100%;">
        <svg viewBox="0 0 40 60" class="w-full h-auto block"
            style="transform: {{ $isUpper ? 'scaleY(1)' : 'scaleY(-1)' }}; display: block; width: 100%; height: auto;">

            <!-- Root (Background) -->
            <path d="M12 20 L15 55 L20 45 L25 55 L28 20 Z"
                fill="{{ $getColor($rootStatus) }}"
                stroke="{{ in_array('root', $activeSurfaces ?? []) ? '#06b6d4' : '#9ca3af' }}"
                stroke-width="{{ in_array('root', $activeSurfaces ?? []) ? '2.5' : '1' }}"
                wire:click="selectSurface({{ $number }}, 'root')" class="hover:opacity-80 transition-opacity" />

            <!-- Crown (Foreground) - Divided into 5 surfaces -->
            <g transform="translate(0, 0)">
                <!-- Background Circle for Crown Base -->
                <circle cx="20" cy="20" r="18" fill="white" stroke="#9ca3af" stroke-width="1" />

                <!-- Top (Vestibular for Upper) -->
                <path d="M6 10 L14 14 L26 14 L34 10 Q20 -2 6 10 Z"
                    fill="{{ $surfaces['top'] ? $getColor($surfaces['top']) : 'white' }}"
                    stroke="{{ in_array('top', $activeSurfaces ?? []) ? '#06b6d4' : '#9ca3af' }}"
                    stroke-width="{{ in_array('top', $activeSurfaces ?? []) ? '2.5' : '0.5' }}"
                    wire:click.stop="selectSurface({{ $number }}, 'top')" class="hover:fill-blue-100" />

                <!-- Bottom (Palatal for Upper) -->
                <path d="M6 30 L14 26 L26 26 L34 30 Q20 42 6 30 Z"
                    fill="{{ $surfaces['bottom'] ? $getColor($surfaces['bottom']) : 'white' }}"
                    stroke="{{ in_array('bottom', $activeSurfaces ?? []) ? '#06b6d4' : '#9ca3af' }}"
                    stroke-width="{{ in_array('bottom', $activeSurfaces ?? []) ? '2.5' : '0.5' }}"
                    wire:click.stop="selectSurface({{ $number }}, 'bottom')" class="hover:fill-blue-100" />

                <!-- Left (Mesial) -->
                <path d="M6 10 L14 14 L14 26 L6 30 Q-2 20 6 10 Z"
                    fill="{{ $surfaces['left'] ? $getColor($surfaces['left']) : 'white' }}"
                    stroke="{{ in_array('left', $activeSurfaces ?? []) ? '#06b6d4' : '#9ca3af' }}"
                    stroke-width="{{ in_array('left', $activeSurfaces ?? []) ? '2.5' : '0.5' }}"
                    wire:click.stop="selectSurface({{ $number }}, 'left')" class="hover:fill-blue-100" />

                <!-- Right (Distal) -->
                <path d="M34 10 L26 14 L26 26 L34 30 Q42 20 34 10 Z"
                    fill="{{ $surfaces['right'] ? $getColor($surfaces['right']) : 'white' }}"
                    stroke="{{ in_array('right', $activeSurfaces ?? []) ? '#06b6d4' : '#9ca3af' }}"
                    stroke-width="{{ in_array('right', $activeSurfaces ?? []) ? '2.5' : '0.5' }}"
                    wire:click.stop="selectSurface({{ $number }}, 'right')" class="hover:fill-blue-100" />

                <!-- Center (Occlusal) -->
                <rect x="14" y="14" width="12" height="12"
                    fill="{{ $surfaces['center'] ? $getColor($surfaces['center']) : 'white' }}"
                    stroke="{{ in_array('center', $activeSurfaces ?? []) ? '#06b6d4' : '#9ca3af' }}"
                    stroke-width="{{ in_array('center', $activeSurfaces ?? []) ? '2.5' : '0.5' }}"
                    wire:click.stop="selectSurface({{ $number }}, 'center')" class="hover:fill-blue-100" />
            </g>
        </svg>
    </div>
</div>