<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Reservar Cita</h2>
        <p class="text-gray-500 mt-2">Completa los pasos para agendar tu próxima visita</p>
    </div>

    <!-- Progress Bar -->
    <div class="mb-10">
        <div class="flex items-center justify-center">
            <div class="flex items-center w-full max-w-lg">
                <!-- Step 1 -->
                <div class="flex flex-col items-center relative">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300
                        @if($step >= 1) bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg shadow-blue-200 @else bg-gray-200 text-gray-500 @endif">
                        @if($step > 1)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        @else 1 @endif
                    </div>
                    <span class="text-xs mt-2 font-medium @if($step >= 1) text-blue-600 @else text-gray-400 @endif">Tratamiento</span>
                </div>
                <!-- Line 1-2 -->
                <div class="flex-1 h-1 mx-4 rounded-full transition-all duration-300 @if($step > 1) bg-gradient-to-r from-blue-600 to-cyan-600 @else bg-gray-200 @endif"></div>
                <!-- Step 2 -->
                <div class="flex flex-col items-center relative">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300
                        @if($step >= 2) bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg shadow-blue-200 @else bg-gray-200 text-gray-500 @endif">
                        @if($step > 2)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        @else 2 @endif
                    </div>
                    <span class="text-xs mt-2 font-medium @if($step >= 2) text-blue-600 @else text-gray-400 @endif">Fecha y Hora</span>
                </div>
                <!-- Line 2-3 -->
                <div class="flex-1 h-1 mx-4 rounded-full transition-all duration-300 @if($step > 2) bg-gradient-to-r from-blue-600 to-cyan-600 @else bg-gray-200 @endif"></div>
                <!-- Step 3 -->
                <div class="flex flex-col items-center relative">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300
                        @if($step >= 3) bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg shadow-blue-200 @else bg-gray-200 text-gray-500 @endif">
                        3
                    </div>
                    <span class="text-xs mt-2 font-medium @if($step >= 3) text-blue-600 @else text-gray-400 @endif">Confirmar</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-xl shadow-gray-200/50 sm:rounded-2xl overflow-hidden border border-gray-100">
        <div class="px-6 py-8 sm:px-8">
            <!-- Step 1: Select Procedure -->
            @if($step === 1)
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Selecciona el Tratamiento</h3>
                    <p class="text-sm text-gray-500 mt-1">¿Qué tipo de cita necesitas?</p>
                </div>

                @if($procedures->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($procedures as $procedure)
                    <button type="button" 
                        wire:click="$set('selectedProcedureId', {{ $procedure->id }})"
                        class="relative rounded-xl border-2 {{ $selectedProcedureId == $procedure->id ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-md' }} p-5 text-left transition-all duration-200 group">
                        @if($selectedProcedureId == $procedure->id)
                        <div class="absolute top-3 right-3">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        @endif
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg {{ $selectedProcedureId == $procedure->id ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500 group-hover:bg-blue-50 group-hover:text-blue-500' }} flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $procedure->procedure_name }}</p>
                                @if($procedure->category)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $procedure->category }}</p>
                                @endif
                                <p class="text-sm font-bold text-blue-600 mt-2">
                                    ${{ number_format($procedure->price, 0, ',', '.') }}
                                </p>
                                @if($procedure->duration)
                                <p class="text-xs text-gray-400 mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $procedure->duration }} min
                                </p>
                                @endif
                            </div>
                        </div>
                    </button>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 bg-gray-50 rounded-xl">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="mt-3 text-gray-500">No hay tratamientos disponibles. Contacta a tu clínica.</p>
                </div>
                @endif
                @error('selectedProcedureId') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            @endif

            <!-- Step 2: Select Date & Time -->
            @if($step === 2)
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Elige Fecha y Hora</h3>
                    <p class="text-sm text-gray-500 mt-1">Selecciona un día y horario disponible</p>
                </div>

                <!-- Date Selection -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                    <input type="date" wire:model.live="selectedDate" id="date" min="{{ date('Y-m-d') }}"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3 px-4">
                    @error('selectedDate') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Time Slots -->
                @if($selectedDate)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Horarios Disponibles</label>
                    @if(count($availableSlots) > 0)
                    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                        @foreach($availableSlots as $slot)
                        <button type="button" wire:click="$set('selectedTimeSlot', '{{ $slot }}')"
                            class="{{ $selectedTimeSlot === $slot ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg shadow-blue-200 border-transparent' : 'bg-white text-gray-700 border-gray-200 hover:border-blue-300 hover:bg-blue-50' }} border-2 rounded-xl py-3 text-sm font-medium transition-all duration-200">
                            {{ $slot }}
                        </button>
                        @endforeach
                    </div>
                    @error('selectedTimeSlot') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                    @else
                    <div class="text-center py-8 bg-gray-50 rounded-xl">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-3 text-gray-500">No hay horarios disponibles para esta fecha.</p>
                        <p class="text-sm text-gray-400 mt-1">Intenta seleccionar otro día.</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endif

            <!-- Step 3: Confirmation -->
            @if($step === 3)
            <div class="space-y-6">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-5">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-green-800">¡Casi listo!</h3>
                            <p class="text-sm text-green-700 mt-1">Revisa los detalles antes de confirmar tu cita.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Resumen de la Cita</h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm text-gray-500">Tratamiento</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900">
                                {{ \App\Models\ProcedurePrice::find($selectedProcedureId)?->procedure_name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Fecha</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Hora</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900">
                                {{ $selectedTimeSlot }} hrs
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Paciente</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900">
                                {{ $patient->name }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-6 py-5 sm:px-8 flex justify-between items-center border-t border-gray-100">
            @if($step > 1)
            <button wire:click="previousStep" type="button"
                class="inline-flex items-center py-2.5 px-5 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Atrás
            </button>
            @else
            <a href="{{ URL::signedRoute('portal.dashboard', ['patient' => $patient]) }}"
                class="inline-flex items-center py-2.5 px-5 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar
            </a>
            @endif

            @if($step < 3)
            <button wire:click="nextStep" type="button"
                class="inline-flex items-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-lg shadow-blue-200">
                Siguiente
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            @else
            <button wire:click="book" type="button" wire:confirm="¿Confirmar esta cita?"
                class="inline-flex items-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all shadow-lg shadow-emerald-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Confirmar Cita
            </button>
            @endif
        </div>
    </div>
</div>
