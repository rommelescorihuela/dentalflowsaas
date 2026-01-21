<div class="flex flex-col gap-2 w-full">
    <!-- Updated Layout: {{ now() }} -->
    <!-- Graph Area -->
    <div class="p-2 bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-800">
        <h2 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-100 px-2">Odontogram</h2>
        
        <!-- Container: Centered, no scroll, compact gaps -->
        <div class="flex flex-col gap-1 items-center w-full" style="display: flex; flex-direction: column; gap: 0.25rem; align-items: center; width: 100%;">
            
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

    <!-- Panel Lateral: List only -->
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 w-full">
        <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-gray-100 px-2">Clinical Records</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($toothMap as $tooth => $surfaces)
                @foreach($surfaces as $surface => $status)
                    @if($status && $status !== 'healthy')
                        <div class="flex justify-between items-start p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow">
                            <div>
                                <div class="font-bold text-gray-800 dark:text-gray-200">Tooth {{ $tooth }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">{{ ucfirst($surface) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-500 mt-1 capitalize">{{ str_replace('_', ' ', $status) }}</div>
                            </div>
                            <button wire:click="deleteRecord({{ $tooth }}, '{{ $surface }}')" 
                                    class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20"
                                    title="Delete Record">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @endif
                @endforeach
            @endforeach
            
            @if(empty($toothMap))
                <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400 italic bg-gray-100 dark:bg-gray-800/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
                    No clinical records found. Select a tooth to add treatment.
                </div>
            @endif
        </div>
    </div>

    <!-- Floating Panel for Adding Treatment (Non-blocking) -->
    @if($selectedTooth && !empty($selectedSurfaces))
        <div class="fixed bottom-4 right-4 z-50 w-full max-w-md pointer-events-auto">
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 border border-gray-200 dark:border-gray-700" 
                 wire:key="modal-{{ $selectedTooth }}-{{ count($selectedSurfaces) }}">
                 
                 <!-- Header -->
                 <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                        Treat Tooth {{ $selectedTooth }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400 block">
                            {{ count($selectedSurfaces) }} Surface(s): {{ implode(', ', $selectedSurfaces) }}
                        </span>
                    </h3>
                    <button wire:click="$set('selectedTooth', null)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit="saveRecord">
                    {{ $this->form }}
                    
                    <div class="mt-6 flex justify-end gap-3">
                         <x-filament::button color="gray" wire:click="$set('selectedTooth', null)">
                            Close
                        </x-filament::button>
                        <x-filament::button type="submit">
                            Save Treatment
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>