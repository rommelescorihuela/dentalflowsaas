@extends('patient-portal.layout')

@section('title', 'Citas')

@section('content')
<div class="flex justify-between items-center mb-6 portal-reveal">
    <h2 class="text-2xl font-display font-bold text-stone-800">Mis Citas</h2>
    <a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}"
        class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-700 transition-all duration-200">
        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Nueva Cita
    </a>
</div>

<!-- Upcoming Appointments -->
<div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-100 mb-8 portal-reveal portal-reveal-delay-1">
    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50">
        <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-2.5 rounded-xl mr-3 shadow-lg shadow-primary-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </span>
            Próximas Citas
        </h3>
    </div>
    <div class="p-6 space-y-4">
        @forelse($upcomingAppointments as $appointment)
        <div class="bg-white border border-gray-100 rounded-2xl shadow-lg shadow-gray-100/50 hover:shadow-xl transition-all duration-300 p-5">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-2xl p-3 shadow-lg shadow-primary-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ $appointment->procedurePrice->procedure_name ?? 'Consulta General' }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $appointment->start_time->format('l, d \d\e F \d\e\l Y') }}<br>
                            {{ $appointment->start_time->format('H:i') }} — {{ $appointment->end_time->format('H:i') }}
                        </p>
                        @if($appointment->notes)
                        <p class="text-sm text-gray-400 mt-2 italic">{{ $appointment->notes }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                        @if($appointment->status === 'scheduled') bg-primary-100 text-primary-700
                        @elseif($appointment->status === 'confirmed') bg-green-100 text-green-700
                        @elseif($appointment->status === 'completed') bg-gray-100 text-gray-700
                        @else bg-red-100 text-red-700 @endif">
                        @switch($appointment->status)
                            @case('scheduled') Programada @break
                            @case('confirmed') Confirmada @break
                            @case('completed') Completada @break
                            @case('cancelled') Cancelada @break
                            @default {{ ucfirst($appointment->status) }}
                        @endswitch
                    </span>
                    @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                    <form action="{{ URL::signedRoute('portal.appointments.cancel', ['patient' => $patient, 'appointment' => $appointment]) }}" method="POST"
                        onsubmit="return confirm('¿Estás seguro de cancelar esta cita?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancelar
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-stone-400">
            <svg class="mx-auto h-14 w-14 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="font-display font-semibold text-lg">No tienes citas programadas</p>
            <p class="text-sm mt-1"><a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}" class="text-primary-600 hover:text-primary-700 font-medium">Reserva una cita ahora</a></p>
        </div>
        @endforelse
    </div>
</div>

<!-- Past Appointments -->
<div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-100 portal-reveal portal-reveal-delay-2">
    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-stone-50 to-transparent">
        <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <span class="bg-stone-500 text-white p-2.5 rounded-xl mr-3 shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            Historial de Citas
        </h3>
    </div>
    <div class="p-6 space-y-3">
        @forelse($pastAppointments as $appointment)
        <div class="bg-[#FFFBF5] border border-stone-100 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 bg-stone-100 text-stone-500 rounded-xl p-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-stone-700">{{ $appointment->procedurePrice->procedure_name ?? 'Consulta General' }}</p>
                        <p class="text-xs text-stone-400">{{ $appointment->start_time->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium
                    @if($appointment->status === 'completed') bg-emerald-100 text-emerald-700
                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-700
                    @else bg-stone-100 text-stone-600 @endif">
                    @switch($appointment->status)
                        @case('completed') Completada @break
                        @case('cancelled') Cancelada @break
                        @default {{ ucfirst($appointment->status) }} @endswitch
                </span>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-stone-400">
            <p class="text-sm">No hay citas pasadas</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
