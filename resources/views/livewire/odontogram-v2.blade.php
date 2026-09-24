@php
    $diagnosisLabels = [
        'caries' => 'Caries', 'filled' => 'Obturación', 'endodontic' => 'Endodoncia',
        'endodontic_multi' => 'Endodoncia multiradicular', 'endo_retreatment' => 'Retratamiento endodóntico',
        'missing' => 'Ausente', 'healthy' => 'Sano', 'crown' => 'Corona',
        'prophylaxis' => 'Profilaxis', 'sealant' => 'Sellante', 'fluoride' => 'Flúor',
        'inlay' => 'Incrustación', 'scaling' => 'Detartraje', 'gingivectomy' => 'Gingivectomía',
        'gingival_contouring' => 'Contorno gingival', 'flap_surgery' => 'Cirugía de colgajo',
        'surgical_extraction' => 'Extracción quirúrgica', 'wisdom_tooth' => 'Cordal',
        'implant' => 'Implante', 'implant_crown' => 'Corona sobre implante',
        'braces_metal' => 'Brackets metálicos', 'braces_aesthetic' => 'Brackets estéticos',
        'retainer_fixed' => 'Contención fija', 'retainer_removable' => 'Contención removible',
        'crown_pfm' => 'Corona metal-porcelana', 'crown_zirconia' => 'Corona de zirconio',
        'bridge' => 'Puente', 'partial_denture' => 'Prótesis parcial', 'full_denture' => 'Prótesis total',
        'whitening' => 'Blanqueamiento', 'veneer_composite' => 'Carilla de composite',
        'veneer_ceramic' => 'Carilla cerámica', 'consultation' => 'Consulta',
        'xray_periapical' => 'Radiografía periapical', 'xray_panoramic' => 'Radiografía panorámica',
        'cbct' => 'Tomografía CBCT',
    ];
    $surfaceLabels = [
        'top' => 'Superior', 'bottom' => 'Inferior', 'left' => 'Izquierda',
        'right' => 'Derecha', 'center' => 'Centro', 'root' => 'Raíz',
    ];
    $label = fn ($key) => $diagnosisLabels[$key] ?? ucfirst(str_replace('_', ' ', (string) $key));
@endphp

<div class="flex flex-col w-full" style="gap: 3rem;">
    <style>
        .odonto-table-container {
            overflow: hidden;
            border-radius: 0.5rem;
            border: 1px solid #e8e4da;
            width: 100%;
        }
        .odonto-table {
            min-width: 100%;
            border-collapse: collapse;
            width: 100%;
        }
        .odonto-th {
            padding: 12px 24px;
            text-align: left;
            font-size: 0.72rem;
            font-weight: 600;
            color: #847d6f;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: #f4f1ea;
        }
        .odonto-td {
            padding: 16px 24px;
            white-space: nowrap;
            font-size: 0.875rem;
            font-weight: 500;
            color: #211f1b;
            background-color: #ffffff;
            border-bottom: 1px solid #e8e4da;
        }
        .odonto-td-secondary {
            color: #847d6f;
            font-weight: 400;
        }

        /* Dark Mode */
        .dark .odonto-table-container {
            border-color: rgba(232, 228, 218, 0.14);
            background-color: transparent;
        }
        .dark .odonto-th {
            background-color: transparent;
            color: #d4d0c6;
            border-bottom: 1px solid rgba(232, 228, 218, 0.14);
        }
        .dark .odonto-td {
            background-color: transparent;
            color: #f4f1ea;
            border-bottom: 1px solid rgba(232, 228, 218, 0.14);
        }
        .dark .odonto-td-secondary {
            color: #a8a396;
        }
        .dark .odonto-table tr:hover .odonto-td {
            background-color: rgba(115, 209, 195, 0.06);
        }
    </style>

    <!-- Graph Area -->
    <div class="p-3 bg-white dark:bg-[#151d1a] rounded-xl shadow-sm border border-[#e8e4da] dark:border-[#243029]">
        <h2 class="text-xl font-bold mb-2 text-[#211f1b] dark:text-sand-100 px-2">Odontograma</h2>

        {{-- Leyenda de diagnósticos en uso --}}
        @php $usedCodes = collect($toothMap)->flatten()->reject(fn ($c) => !$c || $c === 'healthy')->unique(); @endphp
        @if($usedCodes->isNotEmpty())
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 px-2 pb-3 mb-1 border-b border-[#f0ede5] dark:border-white/10">
                <span class="text-xs font-semibold uppercase tracking-wide text-[#847d6f] dark:text-sand-400">Leyenda</span>
                @foreach($usedCodes as $code)
                    <span class="inline-flex items-center gap-1.5 text-xs text-[#625c51] dark:text-sand-300">
                        <span class="w-3 h-3 rounded-sm border border-black/10" style="background-color: {{ $statusColors[$code] ?? '#b0a99a' }};"></span>
                        {{ $label($code) }}
                    </span>
                @endforeach
            </div>
        @endif

        <!-- Container: Centered, no scroll, compact gaps -->
        <div class="w-full overflow-x-auto overflow-y-hidden pb-4">
            <div class="flex flex-col gap-1 items-center min-w-[760px] w-full" style="display: flex; flex-direction: column; gap: 0.25rem; align-items: center; width: 100%;">

            <!-- Upper Jaw: Grid layout for perfect distribution -->
            <div class="flex flex-nowrap w-full pb-1 gap-0.5" style="display: flex; flex-wrap: nowrap; width: 100%; padding-bottom: 0.25rem; gap: 0.125rem;">
                <!-- Right Quadrant (18-11) -->
                <div class="grid grid-cols-8 gap-0.5 flex-1 min-w-0" style="display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: 0.125rem; flex: 1 1 0%; min-width: 0;">
                    @foreach($upperTeethRight as $tooth)
                        @include('components.odontogram.tooth', [
                            'number' => $tooth,
                            'isUpper' => true,
                            'surfaces' => $this->getSurfaces($tooth),
                            'rootStatus' => $this->getSurfaces($tooth)['root'] ?? null,
                            'colors' => $statusColors,
                            'activeSurfaces' => ($selectedTooth === $tooth) ? $selectedSurfaces : []
                        ])
                    @endforeach
                </div>
                <!-- Divider -->
                <div class="border-r-2 border-gray-300 dark:border-gray-600 h-16 shrink-0 mx-0.5" style="border-right-width: 2px; height: 4rem; flex-shrink: 0; margin: 0 0.125rem;"></div>
                <!-- Left Quadrant (21-28) -->
                <div class="grid grid-cols-8 gap-0.5 flex-1 min-w-0" style="display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: 0.125rem; flex: 1 1 0%; min-width: 0;">
                    @foreach($upperTeethLeft as $tooth)
                        @include('components.odontogram.tooth', [
                            'number' => $tooth,
                            'isUpper' => true,
                            'surfaces' => $this->getSurfaces($tooth),
                            'rootStatus' => $this->getSurfaces($tooth)['root'] ?? null,
                            'colors' => $statusColors,
                            'activeSurfaces' => ($selectedTooth === $tooth) ? $selectedSurfaces : []
                        ])
                    @endforeach
                </div>
            </div>

            <!-- Lower Jaw: Grid layout for perfect distribution -->
            <div class="flex flex-nowrap w-full pb-1 gap-0.5" style="display: flex; flex-wrap: nowrap; width: 100%; padding-bottom: 0.25rem; gap: 0.125rem;">
                <!-- Right Quadrant (48-41) -->
                <div class="grid grid-cols-8 gap-0.5 flex-1 min-w-0" style="display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: 0.125rem; flex: 1 1 0%; min-width: 0;">
                    @foreach($lowerTeethRight as $tooth)
                        @include('components.odontogram.tooth', [
                            'number' => $tooth,
                            'isUpper' => false,
                            'surfaces' => $this->getSurfaces($tooth),
                            'rootStatus' => $this->getSurfaces($tooth)['root'] ?? null,
                            'colors' => $statusColors,
                            'activeSurfaces' => ($selectedTooth === $tooth) ? $selectedSurfaces : []
                        ])
                    @endforeach
                </div>
                <!-- Divider -->
                <div class="border-r-2 border-gray-300 dark:border-gray-600 h-16 shrink-0 mx-0.5" style="border-right-width: 2px; height: 4rem; flex-shrink: 0; margin: 0 0.125rem;"></div>
                <!-- Left Quadrant (31-38) -->
                <div class="grid grid-cols-8 gap-0.5 flex-1 min-w-0" style="display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: 0.125rem; flex: 1 1 0%; min-width: 0;">
                    @foreach($lowerTeethLeft as $tooth)
                        @include('components.odontogram.tooth', [
                            'number' => $tooth,
                            'isUpper' => false,
                            'surfaces' => $this->getSurfaces($tooth),
                            'rootStatus' => $this->getSurfaces($tooth)['root'] ?? null,
                            'colors' => $statusColors,
                            'activeSurfaces' => ($selectedTooth === $tooth) ? $selectedSurfaces : []
                        ])
                    @endforeach
                </div>
            </div>

        </div>
        </div>
    </div>

    <!-- Inline Form for Adding Treatment -->
    @if($selectedTooth)
        <div class="bg-[#f4f1ea] dark:bg-[#151d1a] p-6 rounded-xl border border-[#e8e4da] dark:border-[#243029] w-full animate-fade-in-down" style="margin-bottom: 2rem;">
             <!-- Header -->
             <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-bold text-[#211f1b] dark:text-sand-100">
                        Tratar diente {{ $selectedTooth }}
                    </h3>
                    <p class="text-sm text-[#847d6f] dark:text-sand-400 mt-1">
                        Selecciona las superficies y los detalles del diagnóstico.
                    </p>
                </div>
                <button wire:click="$set('selectedTooth', null)" class="text-[#847d6f] hover:text-[#4a453d] dark:hover:text-sand-200 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit="saveRecord">
                {{ $this->form }}

                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-[#e8e4da] dark:border-white/10">
                     <x-filament::button color="gray" wire:click="$set('selectedTooth', null)">
                        Cancelar
                    </x-filament::button>
                    <x-filament::button type="submit">
                        Guardar tratamiento
                    </x-filament::button>
                </div>
            </form>
        </div>
    @endif

    <!-- Panel Lateral: Table List -->
    <div class="bg-[#f4f1ea] dark:bg-[#151d1a] p-4 rounded-xl border border-[#e8e4da] dark:border-[#243029] w-full overflow-hidden">
        <h3 class="font-bold text-lg mb-4 text-[#211f1b] dark:text-sand-100 px-2">Registros clínicos</h3>

        <div class="odonto-table-container">
            <table class="odonto-table">
                <thead>
                    <tr>
                        <th class="odonto-th">Diente</th>
                        <th class="odonto-th">Superficie</th>
                        <th class="odonto-th">Estado</th>
                        <th class="odonto-th" style="text-align: right;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($toothMap as $tooth => $surfaces)
                        @foreach($surfaces as $surface => $status)
                            @if($status && $status !== 'healthy')
                                <tr>
                                    <td class="odonto-td">
                                        {{ $tooth }}
                                    </td>
                                    <td class="odonto-td odonto-td-secondary">
                                        {{ $surfaceLabels[$surface] ?? ucfirst($surface) }}
                                    </td>
                                    <td class="odonto-td">
                                        <span style="display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background-color: #d3f2eb; color: #14645c; text-transform: capitalize;" class="dark:bg-primary-900 dark:text-primary-200">
                                            {{ $label($status) }}
                                        </span>
                                    </td>
                                    <td class="odonto-td" style="text-align: right;">
                                        <button wire:click="deleteRecord({{ $tooth }}, '{{ $surface }}')"
                                                title="Eliminar registro"
                                                style="color: #cf4860; padding: 4px; border-radius: 9999px; cursor: pointer; transition: background-color 0.2s;">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; width: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach

                    @if(empty($toothMap))
                        <tr>
                            <td colspan="4" class="odonto-td" style="text-align: center; font-style: italic;">
                                <span class="dark:text-sand-400">No hay registros clínicos. Selecciona un diente para agregar un tratamiento.</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>


</div>
