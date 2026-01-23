<div class="flex flex-col w-full" style="gap: 3rem;">
    <style>
        .odonto-table-container {
            overflow: hidden; 
            border-radius: 0.5rem; 
            border: 1px solid #e5e7eb; 
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
            font-size: 0.75rem; 
            font-weight: 500; 
            color: #6b7280; 
            text-transform: uppercase; 
            letter-spacing: 0.05em; 
            background-color: #f9fafb;
        }
        .odonto-td { 
            padding: 16px 24px; 
            white-space: nowrap; 
            font-size: 0.875rem; 
            font-weight: 500; 
            color: #111827; 
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb; 
        }
        .odonto-td-secondary {
            color: #6b7280;
            font-weight: 400;
        }
        
        /* Dark Mode */
        .dark .odonto-table-container {
            border-color: #374151;
            background-color: transparent; /* Seamless match */
        }
        .dark .odonto-th { 
            background-color: transparent; /* No distinct block */
            color: #d1d5db; 
            border-bottom: 1px solid #374151;
        }
        .dark .odonto-td { 
            background-color: transparent; 
            color: #f3f4f6; 
            border-bottom: 1px solid #374151; 
        }
        .dark .odonto-td-secondary {
            color: #9ca3af;
        }
        .dark .odonto-table tr:hover .odonto-td {
            background-color: rgba(255, 255, 255, 0.05); /* Subtle hover */
        }
    </style>

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

    <!-- Inline Form for Adding Treatment -->
    @if($selectedTooth)
        <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 w-full animate-fade-in-down" style="margin-bottom: 2rem;">
             <!-- Header -->
             <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                        Treat Tooth {{ $selectedTooth }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Select surfaces and diagnosis details.
                    </p>
                </div>
                <button wire:click="$set('selectedTooth', null)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit="saveRecord">
                {{ $this->form }}
                
                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                     <x-filament::button color="gray" wire:click="$set('selectedTooth', null)">
                        Cancel
                    </x-filament::button>
                    <x-filament::button type="submit">
                        Save Treatment
                    </x-filament::button>
                </div>
            </form>
        </div>
    @endif

    <!-- Panel Lateral: Table List -->
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 w-full overflow-hidden">
        <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-gray-100 px-2">Clinical Records</h3>

        <div class="odonto-table-container">
            <table class="odonto-table">
                <thead>
                    <tr>
                        <th class="odonto-th">Tooth</th>
                        <th class="odonto-th">Surface</th>
                        <th class="odonto-th">Status</th>
                        <th class="odonto-th" style="text-align: right;">Action</th>
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
                                        {{ ucfirst($surface) }}
                                    </td>
                                    <td class="odonto-td">
                                        <span style="display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background-color: #dbeafe; color: #1e40af; text-transform: capitalize;" class="dark:bg-blue-900 dark:text-blue-200">
                                            {{ str_replace('_', ' ', $status) }}
                                        </span>
                                    </td>
                                    <td class="odonto-td" style="text-align: right;">
                                        <button wire:click="deleteRecord({{ $tooth }}, '{{ $surface }}')" 
                                                title="Delete Record"
                                                style="color: #ef4444; padding: 4px; border-radius: 9999px; cursor: pointer; transition: background-color 0.2s;">
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
                                <span class="dark:text-gray-400">No clinical records found. Select a tooth to add treatment.</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>


</div>
