<div class="max-w-3xl mx-auto py-10 px-6">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-stone-800" style="font-family:'Outfit',sans-serif;letter-spacing:-0.02em">Reservar Cita</h2>
        <p class="text-stone-500 mt-2" style="font-family:'Work Sans',sans-serif">Completa los pasos para agendar tu proxima visita</p>
    </div>

    <!-- Progress Bar -->
    <div class="mb-10">
        <div class="flex items-center justify-center">
            <div class="flex items-center w-full max-w-lg">
                <!-- Step 1 -->
                <div class="flex flex-col items-center relative">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300
                        @if($step >= 1) bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-lg shadow-primary-500/20 @else bg-gray-200 text-gray-500 @endif">
                        @if($step > 1)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        @else 1 @endif
                    </div>
                    <span class="text-xs mt-2 font-medium @if($step >= 1) text-primary-600 @else text-gray-400 @endif">Tratamiento</span>
                </div>
                <!-- Line 1-2 -->
                <div class="flex-1 h-1 mx-4 rounded-full transition-all duration-300 @if($step > 1) bg-gradient-to-r from-primary-600 to-primary-700 @else bg-gray-200 @endif"></div>
                <!-- Step 2 -->
                <div class="flex flex-col items-center relative">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300
                        @if($step >= 2) bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-lg shadow-primary-500/20 @else bg-gray-200 text-gray-500 @endif">
                        @if($step > 2)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        @else 2 @endif
                    </div>
                    <span class="text-xs mt-2 font-medium @if($step >= 2) text-primary-600 @else text-gray-400 @endif">Fecha y Hora</span>
                </div>
                <!-- Line 2-3 -->
                <div class="flex-1 h-1 mx-4 rounded-full transition-all duration-300 @if($step > 2) bg-gradient-to-r from-primary-600 to-primary-700 @else bg-gray-200 @endif"></div>
                <!-- Step 3 -->
                <div class="flex flex-col items-center relative">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300
                        @if($step >= 3) bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-lg shadow-primary-500/20 @else bg-gray-200 text-gray-500 @endif">
                        3
                    </div>
                    <span class="text-xs mt-2 font-medium @if($step >= 3) text-primary-600 @else text-gray-400 @endif">Confirmar</span>
                </div>
                <span class="text-xs mt-2 font-semibold @if($step >= 2) text-teal-600 @else text-stone-400 @endif" style="font-family:'Work Sans',sans-serif">Fecha y Hora</span>
            </div>
            <div class="flex-1 h-0.5 mx-3 rounded-full transition-all duration-300 @if($step > 2) bg-teal-600 @else bg-stone-200 @endif"></div>
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm transition-all duration-300 @if($step >= 3) bg-gradient-to-br from-teal-600 to-teal-700 text-white shadow-lg shadow-teal-600/20 @else bg-stone-200 text-stone-500 @endif" style="font-family:'Outfit',sans-serif">3</div>
                <span class="text-xs mt-2 font-semibold @if($step >= 3) text-teal-600 @else text-stone-400 @endif" style="font-family:'Work Sans',sans-serif">Confirmar</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] border border-teal-500/10 shadow-lg overflow-hidden">
        <div class="px-6 py-8 sm:px-8" style="font-family:'Work Sans',sans-serif">
            <!-- Step 1 -->
            @if($step === 1)
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-stone-800" style="font-family:'Outfit',sans-serif">Selecciona el Tratamiento</h3>
                    <p class="text-sm text-stone-500 mt-1">Que tipo de cita necesitas?</p>
                </div>

                @if($procedures->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($procedures as $procedure)
                    <button type="button" 
                        wire:click="$set('selectedProcedureId', {{ $procedure->id }})"
                        class="relative rounded-xl border-2 {{ $selectedProcedureId == $procedure->id ? 'border-primary-500 bg-primary-50 ring-2 ring-primary-200' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-md' }} p-5 text-left transition-all duration-200 group">
                        @if($selectedProcedureId == $procedure->id)
                        <div class="absolute top-3 right-3">
                            <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        @endif
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg {{ $selectedProcedureId == $procedure->id ? 'bg-primary-100 text-primary-600' : 'bg-gray-100 text-gray-500 group-hover:bg-primary-50 group-hover:text-primary-500' }} flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $procedure->procedure_name }}</p>
                                @if($procedure->category)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $procedure->category }}</p>
                                @endif
                                <p class="text-sm font-bold text-primary-600 mt-2">
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
                <div class="text-center py-12 bg-[#FFFBF5] rounded-2xl text-stone-400">
                    <svg class="mx-auto h-12 w-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="font-semibold">Sin tratamientos disponibles</p>
                    <p class="text-sm mt-1">Contacta a tu clinica.</p>
                </div>
                @endif
                @error('selectedProcedureId') <p class="text-rose-500 text-sm">{{ $message }}</p> @enderror
            </div>
            @endif

            <!-- Step 2 -->
            @if($step === 2)
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-stone-800" style="font-family:'Outfit',sans-serif">Elige Fecha y Hora</h3>
                    <p class="text-sm text-stone-500 mt-1">Selecciona un dia y horario disponible</p>
                </div>
                <div>
                    <label for="date" class="block text-sm font-semibold text-stone-700 mb-2">Fecha</label>
                    <input type="date" wire:model.live="selectedDate" id="date" min="{{ date('Y-m-d') }}"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm py-3 px-4">
                    @error('selectedDate') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                @if($selectedDate)
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-3">Horarios Disponibles</label>
                    @if(count($availableSlots) > 0)
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                        @foreach($availableSlots as $slot)
                        <button type="button" wire:click="$set('selectedTimeSlot', '{{ $slot }}')"
                            class="{{ $selectedTimeSlot === $slot ? 'bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-lg shadow-primary-500/20 border-transparent' : 'bg-white text-gray-700 border-gray-200 hover:border-primary-300 hover:bg-primary-50' }} border-2 rounded-xl py-3 text-sm font-medium transition-all duration-200">
                            {{ $slot }}
                        </button>
                        @endforeach
                    </div>
                    @error('selectedTimeSlot') <p class="text-rose-500 text-sm mt-2">{{ $message }}</p> @enderror
                    @else
                    <div class="text-center py-12 bg-[#FFFBF5] rounded-2xl text-stone-400">
                        <svg class="mx-auto h-12 w-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-semibold">Sin horarios disponibles</p>
                        <p class="text-sm mt-1">Intenta seleccionar otro dia.</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endif

            <!-- Step 3 -->
            @if($step === 3)
            <div class="space-y-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-emerald-800" style="font-family:'Outfit',sans-serif">Casi listo!</h3>
                        <p class="text-sm text-emerald-700 mt-1">Revisa los detalles antes de confirmar tu cita.</p>
                    </div>
                </div>
                <div class="bg-[#FFFBF5] border border-stone-100 rounded-2xl p-6">
                    <h4 class="text-xs font-semibold text-stone-400 uppercase tracking-wide mb-5">Resumen de la Cita</h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div><dt class="text-xs text-stone-400 uppercase tracking-wide font-semibold">Tratamiento</dt><dd class="mt-1 text-lg font-bold text-stone-800" style="font-family:'Outfit',sans-serif">{{ \App\Models\ProcedurePrice::find($selectedProcedureId)?->procedure_name }}</dd></div>
                        <div><dt class="text-xs text-stone-400 uppercase tracking-wide font-semibold">Fecha</dt><dd class="mt-1 text-lg font-bold text-stone-800" style="font-family:'Outfit',sans-serif">{{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</dd></div>
                        <div><dt class="text-xs text-stone-400 uppercase tracking-wide font-semibold">Hora</dt><dd class="mt-1 text-lg font-bold text-stone-800" style="font-family:'Outfit',sans-serif">{{ $selectedTimeSlot }} hrs</dd></div>
                        <div><dt class="text-xs text-stone-400 uppercase tracking-wide font-semibold">Paciente</dt><dd class="mt-1 text-lg font-bold text-stone-800" style="font-family:'Outfit',sans-serif">{{ $patient->name }}</dd></div>
                    </dl>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-[#FFFBF5] px-6 py-5 sm:px-8 flex justify-between items-center border-t border-stone-100">
            @if($step > 1)
            <button wire:click="previousStep" type="button"
                class="inline-flex items-center py-2.5 px-5 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Atrás
            </button>
            @else
            <a href="{{ URL::signedRoute('portal.dashboard', ['patient' => $patient]) }}"
                class="inline-flex items-center py-2.5 px-5 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar
            </a>
            @endif

            @if($step < 3)
            <button wire:click="nextStep" type="button"
                class="inline-flex items-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all shadow-lg shadow-primary-500/20">
                Siguiente
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            @else
            <button wire:click="book" type="button" wire:confirm="Confirmar esta cita?" class="inline-flex items-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-semibold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20" style="font-family:'Work Sans',sans-serif">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Confirmar Cita
            </button>
            @endif
        </div>
    </div>
</div>
