<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal de Paciente - {{ $patient->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-cyan-50 font-sans antialiased min-h-screen">
    <!-- Decorative background elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
    </div>

    <!-- Skip link for accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded-md z-50">
        Saltar al contenido principal
    </a>
    
    <div class="relative min-h-screen">
        <!-- Header -->
        <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-blue-200">
                        {{ strtoupper(substr($patient->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Hola, {{ $patient->name }}
                        </h1>
                        <p class="text-sm text-gray-500">Bienvenido a tu portal de paciente</p>
                    </div>
                </div>
                <span class="hidden sm:inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-full text-sm font-medium shadow-lg shadow-blue-200">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Dental Portal
                </span>
            </div>
        </header>

        <main id="main-content" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl shadow-md" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white/80 backdrop-blur-md rounded-2xl p-5 shadow-lg shadow-gray-200/50 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Próximas Citas</p>
                            <p class="text-3xl font-bold text-blue-600 mt-1">
                                {{ $patient->appointments()->where('start_time', '>=', now())->count() }}
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-md rounded-2xl p-5 shadow-lg shadow-gray-200/50 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Presupuestos Pendientes</p>
                            <p class="text-3xl font-bold text-amber-600 mt-1">
                                {{ $patient->budgets()->where('status', 'sent')->count() }}
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-md rounded-2xl p-5 shadow-lg shadow-gray-200/50 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Gastado</p>
                            <p class="text-3xl font-bold text-emerald-600 mt-1">
                                ${{ number_format($patient->payments()->where('status', 'paid')->sum('amount'), 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="mb-8 flex justify-end">
                <a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}"
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Reservar Cita
                </a>
            </div>

            <!-- Patient Profile Card -->
            <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 mb-8 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <span class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white p-2 rounded-lg mr-3 shadow-lg shadow-blue-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        Mi Perfil
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $patient->name }}</dd>
                                </div>
                                @if($patient->email)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->email }}</dd>
                                </div>
                                @endif
                                @if($patient->phone)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->phone }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                        <div>
                            <dl class="space-y-4">
                                @if($patient->rut)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">RUT / DNI</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->rut }}</dd>
                                </div>
                                @endif
                                @if($patient->birth_date)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fecha de Nacimiento</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->birth_date->format('d/m/Y') }}</dd>
                                </div>
                                @endif
                                @if($patient->allergies && count($patient->allergies) > 0)
                                <div>
                                    <dt class="text-sm font-medium text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Alergias
                                    </dt>
                                    <dd class="mt-2 flex flex-wrap gap-2">
                                        @foreach($patient->allergies as $allergy => $severity)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $allergy }}
                                            @if($severity)
                                            <span class="ml-1 text-red-600">({{ $severity }})</span>
                                            @endif
                                        </span>
                                        @endforeach
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Próximas Citas Section -->
                <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-100 hover:shadow-2xl transition-all duration-300">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <span class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white p-2.5 rounded-xl mr-3 shadow-lg shadow-blue-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                Próximas Citas
                            </h3>
                        </div>
                    </div>

                    <div class="p-6 bg-gradient-to-b from-gray-50/50 to-white">
                        <div class="space-y-4">
                            @forelse($patient->appointments()->where('start_time', '>=', now())->orderBy('start_time')->get() as $appointment)
                            <div class="bg-white border border-gray-100 rounded-2xl shadow-lg shadow-gray-100/50 hover:shadow-xl hover:shadow-blue-100/50 transition-all duration-300 p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center group cursor-pointer transform hover:-translate-y-1">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-cyan-500 text-white rounded-2xl p-3 group-hover:from-blue-600 group-hover:to-cyan-600 transition-all duration-300 shadow-lg shadow-blue-200">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                            {{ $appointment->procedurePrice->procedure_name ?? 'Consulta General' }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $appointment->start_time->format('d M, Y') }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $appointment->start_time->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0">
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                                        @if($appointment->status === 'scheduled') bg-blue-100 text-blue-700
                                        @elseif($appointment->status === 'confirmed') bg-green-100 text-green-700
                                        @elseif($appointment->status === 'completed') bg-gray-100 text-gray-700
                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        <span class="w-2 h-2 rounded-full mr-2
                                            @if($appointment->status === 'scheduled') bg-blue-500 animate-pulse
                                            @elseif($appointment->status === 'confirmed') bg-green-500
                                            @elseif($appointment->status === 'completed') bg-gray-500
                                            @elseif($appointment->status === 'cancelled') bg-red-500
                                            @else bg-gray-500 @endif"></span>
                                        @switch($appointment->status)
                                            @case('scheduled') Programada @break
                                            @case('confirmed') Confirmada @break
                                            @case('completed') Completada @break
                                            @case('cancelled') Cancelada @break
                                            @default {{ ucfirst($appointment->status) }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-4 text-gray-500 text-lg">No tienes citas programadas</p>
                                <p class="mt-2 text-gray-400 text-sm">¡Reserva tu primera cita ahora!</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Budgets Section -->
                <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-100 hover:shadow-2xl transition-all duration-300">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <span class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-2.5 rounded-xl mr-3 shadow-lg shadow-emerald-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </span>
                                Presupuestos
                            </h3>
                        </div>
                    </div>

                    <div class="p-6 bg-gradient-to-b from-gray-50/50 to-white">
                        <ul class="space-y-4">
                            @forelse($patient->budgets()->orderBy('created_at', 'desc')->get() as $budget)
                            <li class="bg-white border border-gray-100 rounded-2xl shadow-lg shadow-gray-100/50 hover:shadow-xl hover:shadow-emerald-100/50 transition-all duration-300 p-5 group cursor-pointer transform hover:-translate-y-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-teal-500 text-white rounded-2xl p-3 shadow-lg shadow-emerald-200 group-hover:from-emerald-600 group-hover:to-teal-600 transition-all duration-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                                ${{ number_format($budget->total, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Vence: {{ $budget->expires_at?->format('d M Y') ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="px-4 py-2 rounded-full text-xs font-semibold shadow-lg
                                            @if($budget->status === 'accepted') bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 shadow-green-200
                                            @elseif($budget->status === 'sent') bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-700 shadow-yellow-200
                                            @elseif($budget->status === 'rejected') bg-gradient-to-r from-red-100 to-rose-100 text-red-700 shadow-red-200
                                            @else bg-gradient-to-r from-gray-100 to-slate-100 text-gray-700 shadow-gray-200 @endif">
                                            <span class="w-2 h-2 rounded-full mr-2 inline-block
                                                @if($budget->status === 'accepted') bg-green-500
                                                @elseif($budget->status === 'sent') bg-yellow-500 animate-pulse
                                                @elseif($budget->status === 'rejected') bg-red-500
                                                @else bg-gray-500 @endif"></span>
                                            @switch($budget->status)
                                                @case('accepted') Aceptado @break
                                                @case('sent') Enviado @break
                                                @case('rejected') Rechazado @break
                                                @case('draft') Borrador @break
                                                @default {{ ucfirst($budget->status) }}
                                            @endswitch
                                        </span>
                                        @if($budget->status === 'sent')
                                        <form action="{{ URL::signedRoute('portal.budgets.accept', ['budget' => $budget]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-lg shadow-emerald-200 transition-all duration-200 transform hover:scale-105">
                                                Aceptar
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 mb-4">
                                    <svg class="h-8 w-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">No hay presupuestos</h3>
                                <p class="mt-1 text-sm text-gray-500">Los presupuestos de tu tratamiento aparecerán aquí.</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
