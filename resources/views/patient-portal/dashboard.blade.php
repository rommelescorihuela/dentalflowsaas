@extends('patient-portal.layout')

@section('title', 'Inicio')

@section('content')
<!-- Banners -->
@if($banners->isNotEmpty())
<div class="space-y-3 mb-8">
    @foreach($banners as $banner)
        @php
            $colorMap = [
                'blue' => 'bg-blue-50 border-blue-400 text-blue-800',
                'green' => 'bg-green-50 border-green-400 text-green-800',
                'yellow' => 'bg-yellow-50 border-yellow-400 text-yellow-800',
                'red' => 'bg-red-50 border-red-400 text-red-800',
                'purple' => 'bg-purple-50 border-purple-400 text-purple-800',
                'cyan' => 'bg-cyan-50 border-cyan-400 text-cyan-800',
            ];
            $color = $colorMap[$banner->color] ?? $colorMap['blue'];

            $iconMap = [
                'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                'error' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                'promo' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
            ];
            $iconPath = $iconMap[$banner->type] ?? $iconMap['info'];
        @endphp

        <div class="flex items-start gap-3 rounded-lg border-l-4 p-4 {{ $color }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/></svg>
            <div class="flex-1">
                <h4 class="text-sm font-semibold">{{ $banner->title }}</h4>
                @if($banner->message)
                    <p class="mt-1 text-sm opacity-90">{{ $banner->message }}</p>
                @endif
                @if($banner->link)
                    <a href="{{ $banner->link }}" class="mt-2 inline-block text-sm font-medium underline" target="_blank">
                        Ver más
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endif

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8 portal-reveal">
    <div class="bg-white rounded-3xl border border-teal-500/10 shadow-lg p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Próximas Citas</p>
                <p class="text-3xl font-bold text-primary-600 mt-1">{{ $patient->appointments()->where('start_time', '>=', now())->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-3xl border border-teal-500/10 shadow-lg p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-stone-500">Presupuestos Pendientes</p>
                <p class="text-3xl font-display font-bold text-amber-600 mt-1">{{ $patient->budgets()->where('status', 'sent')->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-3xl border border-teal-500/10 shadow-lg p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-stone-500">Total Pagado</p>
                <p class="text-3xl font-display font-bold text-emerald-700 mt-1">${{ number_format($patient->payments()->sum('amount'), 0, ',', '.') }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
</div>

<!-- Action Button -->
<div class="mb-8 flex justify-end portal-reveal portal-reveal-delay-1">
    <a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}"
        class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:scale-105">
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Reservar Cita
    </a>
</div>

<!-- Patient Profile Card -->
<div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 mb-8 overflow-hidden portal-reveal portal-reveal-delay-1">
    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50">
        <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-2 rounded-lg mr-3 shadow-lg shadow-primary-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </span>
            Mi Perfil
        </h3>
    </div>
    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-5">
                <div><dt class="text-xs font-semibold text-stone-400 uppercase tracking-wide">Nombre Completo</dt><dd class="mt-1 text-stone-800 font-semibold text-lg">{{ $patient->name }}</dd></div>
                @if($patient->email)<div><dt class="text-xs font-semibold text-stone-400 uppercase tracking-wide">Email</dt><dd class="mt-1 text-stone-700">{{ $patient->email }}</dd></div>@endif
                @if($patient->phone)<div><dt class="text-xs font-semibold text-stone-400 uppercase tracking-wide">Teléfono</dt><dd class="mt-1 text-stone-700">{{ $patient->phone }}</dd></div>@endif
            </div>
            <div class="space-y-5">
                @if($patient->rut)<div><dt class="text-xs font-semibold text-stone-400 uppercase tracking-wide">RUT / DNI</dt><dd class="mt-1 text-stone-700">{{ $patient->rut }}</dd></div>@endif
                @if($patient->birth_date)<div><dt class="text-xs font-semibold text-stone-400 uppercase tracking-wide">Fecha de Nacimiento</dt><dd class="mt-1 text-stone-700">{{ $patient->birth_date->format('d/m/Y') }}</dd></div>@endif
                @if($patient->allergies && count($patient->allergies) > 0)
                <div>
                    <dt class="text-xs font-semibold text-rose-600 uppercase tracking-wide flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Alergias
                    </dt>
                    <dd class="mt-2 flex flex-wrap gap-2">
                        @foreach($patient->allergies as $allergy => $severity)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">{{ $allergy }}@if($severity)<span class="ml-1 text-rose-600">({{ $severity }})</span>@endif</span>
                        @endforeach
                    </dd>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Appointments -->
<div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-100 mb-8 portal-reveal portal-reveal-delay-2">
    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-2.5 rounded-xl mr-3 shadow-lg shadow-primary-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
                Próximas Citas
            </h3>
            <a href="{{ URL::signedRoute('portal.appointments', ['patient' => $patient]) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Ver todas</a>
        </div>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @forelse($patient->appointments()->where('start_time', '>=', now())->orderBy('start_time')->take(3)->get() as $appointment)
            <div class="bg-white border border-gray-100 rounded-2xl shadow-lg shadow-gray-100/50 hover:shadow-xl transition-all duration-300 p-5 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-2xl p-3 shadow-lg shadow-primary-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ $appointment->procedurePrice->procedure_name ?? 'Consulta General' }}</p>
                        <p class="text-sm text-gray-500">{{ $appointment->start_time->format('d M, Y \a \l\a\s H:i') }}</p>
                    </div>
                </div>
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
            </div>
            @empty
            <div class="text-center py-12 text-stone-400">
                <svg class="mx-auto h-14 w-14 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="font-display font-semibold text-lg">Sin citas programadas</p>
                <p class="text-sm mt-1"><a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}" class="text-primary-600 hover:text-primary-700 font-medium">Reserva tu primera cita</a></p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@if($patient->status === 'active')
<!-- Recent Budgets -->
<div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden portal-reveal portal-reveal-delay-3">
    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-transparent">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="bg-emerald-600 text-white p-2.5 rounded-xl mr-3 shadow-md shadow-emerald-600/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                Presupuestos Recientes
            </h3>
        </div>
    </div>
    <div class="p-6 space-y-3">
        @forelse($patient->budgets()->orderBy('created_at', 'desc')->take(5)->get() as $budget)
        <a href="{{ URL::signedRoute('portal.budgets.view', ['patient' => $patient, 'budget' => $budget]) }}" class="block bg-[#FFFBF5] border border-stone-100 rounded-2xl p-5 hover:border-teal-200 hover:shadow-md transition-all duration-200 group">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-200 transition-colors flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-stone-800">{{ $budget->created_at->format('d/m/Y') }}</p>
                        <p class="text-xs text-stone-500">{{ $budget->items_count ?? $budget->items()->count() }} tratamientos</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-lg font-bold text-primary-600">${{ number_format($budget->total, 0, ',', '.') }}</span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold border
                        @if($budget->status === 'accepted') bg-emerald-50 text-emerald-700 border-emerald-200
                        @elseif($budget->status === 'sent') bg-amber-50 text-amber-700 border-amber-200
                        @elseif($budget->status === 'rejected') bg-rose-50 text-rose-700 border-rose-200
                        @else bg-stone-100 text-stone-600 border-stone-200 @endif">
                        @switch($budget->status)
                            @case('accepted') Aceptado @break
                            @case('sent') Enviado @break
                            @case('rejected') Rechazado @break
                            @case('draft') Borrador @break
                            @default {{ ucfirst($budget->status) }}
                        @endswitch
                    </span>
                    <svg class="w-5 h-5 text-stone-300 group-hover:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-12 text-stone-400">
            <svg class="mx-auto h-14 w-14 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="font-display font-semibold text-lg">Sin presupuestos</p>
            <p class="text-sm mt-1">Aparecerán cuando tu clínica los envíe</p>
        </div>
        @endforelse
    </div>
</div>
@endif
@endsection
