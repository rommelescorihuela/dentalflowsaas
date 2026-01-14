<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Graph Area -->
    <div class="col-span-2 p-6 bg-white rounded-xl shadow-lg">
        <h2 class="text-xl font-bold mb-6">Odontogram</h2>
        
        <!-- Container: Centered, no scroll, compact gaps -->
        <div class="flex flex-col gap-6 items-center w-full" style="display: flex; flex-direction: column; gap: 1.5rem; align-items: center; width: 100%;">
            
            <!-- Upper Jaw: Force single row -->
            <div class="flex flex-nowrap justify-center gap-2 sm:gap-4" style="display: flex; flex-wrap: nowrap; justify-content: center; gap: 0.5rem; max-width: 100%;">
                <!-- Right Quadrant (18-11) -->
                <div class="flex gap-px sm:gap-1" style="display: flex; gap: 2px;">
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
                <div class="border-r-2 border-gray-300 h-16 mx-2" style="border-right-width: 2px; height: 4rem; margin: 0 0.5rem;"></div>
                <!-- Left Quadrant (21-28) -->
                <div class="flex gap-px sm:gap-1" style="display: flex; gap: 2px;">
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

            <!-- Lower Jaw: Force single row -->
            <div class="flex flex-nowrap justify-center gap-2 sm:gap-4" style="display: flex; flex-wrap: nowrap; justify-content: center; gap: 0.5rem; max-width: 100%;">
                <!-- Right Quadrant (48-41) -->
                <div class="flex gap-px sm:gap-1" style="display: flex; gap: 2px;">
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
                <div class="border-r-2 border-gray-300 h-16 mx-2" style="border-right-width: 2px; height: 4rem; margin: 0 0.5rem;"></div>
                <!-- Left Quadrant (31-38) -->
                <div class="flex gap-px sm:gap-1" style="display: flex; gap: 2px;">
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
    <div class="col-span-1 bg-gray-50 p-6 rounded-xl border border-gray-200" style="background-color: #f9fafb; padding: 1.5rem; border-radius: 0.75rem;">
        <h3 class="font-bold text-lg mb-4">Clinical Records</h3>

        <div class="space-y-3" style="display: flex; flex-direction: column; gap: 0.75rem;">
            @foreach($toothMap as $tooth => $surfaces)
                @foreach($surfaces as $surface => $status)
                    @if($status && $status !== 'healthy')
                        <div class="flex justify-between items-center p-3 bg-white rounded border border-gray-200 hover:shadow-sm" style="display: flex; justify-content: space-between; padding: 0.75rem; background-color: white; border: 1px solid #e5e7eb; border-radius: 0.25rem;">
                            <div>
                                <div class="font-bold text-gray-800">Tooth {{ $tooth }} - {{ ucfirst($surface) }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($status) }}</div>
                            </div>
                            <button wire:click="deleteRecord({{ $tooth }}, '{{ $surface }}')" class="text-red-500 hover:text-red-700">
                                Delete
                            </button>
                        </div>
                    @endif
                @endforeach
            @endforeach
            
            @if(empty($toothMap))
                <div class="text-sm text-gray-500 italic">No records found.</div>
            @endif
        </div>
    </div>

    <!-- Floating Panel for Adding Treatment (Non-blocking) -->
    @if($selectedTooth && !empty($selectedSurfaces))
        <div class="fixed bottom-4 right-4 z-50 w-full max-w-md pointer-events-auto" 
             style="position: fixed; bottom: 1rem; right: 1rem; z-index: 50; width: 100%; max-width: 28rem;">
            
            <div class="bg-white rounded-xl shadow-2xl p-6 border border-gray-200" 
                 style="background-color: white; border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #e5e7eb; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);"
                 wire:key="modal-{{ $selectedTooth }}-{{ count($selectedSurfaces) }}">
                 
                 <!-- Header -->
                 <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">
                        Treat Tooth {{ $selectedTooth }}
                        <span class="text-sm font-normal text-gray-500 block">
                            {{ count($selectedSurfaces) }} Surface(s): {{ implode(', ', $selectedSurfaces) }}
                        </span>
                    </h3>
                    <button wire:click="$set('selectedTooth', null)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit="saveRecord">
                    {{ $this->form }}
                    
                    <div class="mt-6 flex justify-end gap-3" style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
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