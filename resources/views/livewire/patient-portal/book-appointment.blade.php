<div class="max-w-3xl mx-auto py-6">
    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                    style="width: {{ ($step / 3) * 100 }}%"></div>
            </div>
            <span class="ml-4 text-sm font-medium text-gray-700">Paso {{ $step }} de 3</span>
        </div>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">

            <!-- Step 1: Select Procedure -->
            @if($step === 1)
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Selecciona el Tratamiento</h3>
                    <p class="mt-1 text-sm text-gray-500">¿Qué tipo de cita necesitas?</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($procedures as $procedure)
                    <div wire:click="$set('selectedProcedureId', {{ $procedure->id }})"
                        class="relative rounded-lg border {{ $selectedProcedureId == $procedure->id ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-300' }} bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 cursor-pointer">
                        <div class="flex-1 min-w-0">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            <p class="text-sm font-medium text-gray-900">{{ $procedure->procedure_name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $procedure->formatted_price ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('selectedProcedureId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            @endif

            <!-- Step 2: Select Date & Time -->
            @if($step === 2)
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Elige Fecha y Hora</h3>
                    <p class="mt-1 text-sm text-gray-500">Horarios disponibles para la próxima semana.</p>
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" wire:model.live="selectedDate" id="date" min="{{ date('Y-m-d') }}"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    @error('selectedDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                @if($selectedDate)
                @if(count($availableSlots) > 0)
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-6 mt-4">
                    @foreach($availableSlots as $slot)
                    <button type="button" wire:click="$set('selectedTimeSlot', '{{ $slot }}')"
                        class="{{ $selectedTimeSlot === $slot ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }} border rounded-md py-2 text-sm font-medium flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ $slot }}
                    </button>
                    @endforeach
                </div>
                @error('selectedTimeSlot') <span class="text-red-500 text-sm block mt-2">{{ $message }}</span> @enderror
                @else
                <div class="text-center py-4 text-gray-500 bg-gray-50 rounded-md mt-4">
                    No hay citas disponibles para esta fecha.
                </div>
                @endif
                @endif
            </div>
            @endif

            <!-- Step 3: Confirmation -->
            @if($step === 3)
            <div class="space-y-6">
                <div class="bg-green-50 border-l-4 border-green-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <!-- Heroicon name: solid/check-circle -->
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Casi listo!</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>Por favor revisa los detalles antes de confirmar.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Tratamiento</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ \App\Models\ProcedurePrice::find($selectedProcedureId)?->procedure_name }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Fecha y Hora</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $selectedDate }} a las {{ $selectedTimeSlot }}</dd>
                    </div>
                </dl>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between">
            @if($step > 1)
            <button wire:click="previousStep" type="button"
                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Atrás
            </button>
            @else
            <div></div>
            @endif

            @if($step < 3) <button wire:click="nextStep" type="button"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Siguiente
                </button>
                @else
                <button wire:click="book" type="button"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Confirmar Cita
                </button>
                @endif
        </div>
    </div>
</div>