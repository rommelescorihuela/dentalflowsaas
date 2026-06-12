<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal del Paciente — {{ $patient->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800|work-sans:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --teal: #0D9488;
            --teal-dark: #0F766E;
            --gold: #F59E0B;
            --gold-light: #FEF3C7;
            --ink: #292524;
        }
        body {
            font-family: 'Work Sans', sans-serif;
            background: #FFFBF5;
            color: var(--ink);
            overflow-x: hidden;
        }
        .font-display { font-family: 'Outfit', sans-serif; letter-spacing: -0.02em; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-16px); } }
        @keyframes orbit { 0% { transform: translate(0,0) rotate(0deg); } 50% { transform: translate(15%,-8%) rotate(2deg); } 100% { transform: translate(0,0) rotate(0deg); } }
        @keyframes orbit-reverse { 0% { transform: translate(0,0) rotate(0deg); } 50% { transform: translate(-10%,5%) rotate(-1.5deg); } 100% { transform: translate(0,0) rotate(0deg); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 6s ease-in-out 3s infinite; }
        .animate-orbit { animation: orbit 16s ease-in-out infinite; }
        .animate-orbit-reverse { animation: orbit-reverse 20s ease-in-out infinite; }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; }
        }
    </style>
</head>

<body class="antialiased min-h-screen">
    <!-- Ambient orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 -left-32 w-[600px] h-[600px] bg-teal-400/10 rounded-full blur-3xl animate-orbit"></div>
        <div class="absolute bottom-1/4 -right-32 w-[500px] h-[500px] bg-amber-200/30 rounded-full blur-3xl animate-orbit-reverse"></div>
    </div>

    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-teal-600 text-white px-4 py-2 rounded-lg z-50">Saltar al contenido</a>

    <div class="relative min-h-screen">
        <!-- Header -->
        <header class="sticky top-0 z-40 bg-[#FFFBF5]/80 backdrop-blur-xl border-b border-teal-500/10">
            <div class="max-w-5xl mx-auto py-5 px-6 lg:px-12 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-teal-600 to-teal-700 flex items-center justify-center text-white text-lg font-display font-bold shadow-lg shadow-teal-600/20">
                        {{ strtoupper(substr($patient->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-xl font-display font-bold text-stone-800">Hola, {{ $patient->name }}</h1>
                        <p class="text-sm text-stone-500">Portal del Paciente</p>
                    </div>
                </div>
                <span class="hidden sm:inline-flex items-center px-4 py-2 bg-teal-50 text-teal-700 rounded-full text-sm font-semibold border border-teal-100">
                    <svg class="w-4 h-4 mr-1.5 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    DentalFlow
                </span>
            </div>
        </header>

        <main id="main-content" class="max-w-5xl mx-auto py-8 px-6 lg:px-12">
            @if (session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
                <div class="bg-white rounded-3xl border border-teal-500/10 shadow-lg p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-stone-500">Proximas Citas</p>
                            <p class="text-3xl font-display font-bold text-teal-700 mt-1">{{ $patient->appointments()->where('start_time', '>=', now())->count() }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-teal-100 flex items-center justify-center text-teal-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
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

            <!-- Reserve button -->
            <div class="mb-10 flex justify-end">
                <a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}" class="inline-flex items-center px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/20 hover:shadow-teal-600/30 transition-all duration-200 hover:-translate-y-0.5">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Reservar Cita
                </a>
            </div>

            <!-- Profile -->
            <div class="bg-white rounded-[2rem] border border-teal-500/10 shadow-lg overflow-hidden mb-10">
                <div class="px-8 py-5 border-b border-stone-100 bg-gradient-to-r from-teal-50 to-transparent">
                    <h3 class="text-xl font-display font-bold text-stone-800 flex items-center gap-3">
                        <span class="bg-teal-600 text-white p-2.5 rounded-xl shadow-md shadow-teal-600/20">
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
                            @if($patient->phone)<div><dt class="text-xs font-semibold text-stone-400 uppercase tracking-wide">Telefono</dt><dd class="mt-1 text-stone-700">{{ $patient->phone }}</dd></div>@endif
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Appointments -->
                <div class="bg-white rounded-[2rem] border border-teal-500/10 shadow-lg overflow-hidden">
                    <div class="px-8 py-5 border-b border-stone-100 bg-gradient-to-r from-teal-50 to-transparent">
                        <h3 class="text-xl font-display font-bold text-stone-800 flex items-center gap-3">
                            <span class="bg-teal-600 text-white p-2.5 rounded-xl shadow-md shadow-teal-600/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            Proximas Citas
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @forelse($patient->appointments()->where('start_time', '>=', now())->orderBy('start_time')->get() as $appointment)
                        <div class="bg-[#FFFBF5] border border-stone-100 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:border-teal-200 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center text-teal-600 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-stone-800">{{ $appointment->procedurePrice->procedure_name ?? 'Consulta General' }}</p>
                                    <p class="text-sm text-stone-500 flex items-center gap-2 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $appointment->start_time->isoFormat('D MMM Y') }}
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $appointment->start_time->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold gap-1.5
                                @if($appointment->status === 'scheduled') bg-teal-50 text-teal-700 border border-teal-200
                                @elseif($appointment->status === 'confirmed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($appointment->status === 'completed') bg-stone-100 text-stone-600 border border-stone-200
                                @else bg-rose-50 text-rose-700 border border-rose-200 @endif">
                                <span class="w-2 h-2 rounded-full @if($appointment->status === 'scheduled') bg-teal-500 animate-pulse @elseif($appointment->status === 'confirmed') bg-emerald-500 @else bg-current @endif"></span>
                                @switch($appointment->status)
                                    @case('scheduled') Programada @break @case('confirmed') Confirmada @break @case('completed') Completada @break @case('cancelled') Cancelada @break @default {{ $appointment->status }}
                                @endswitch
                            </span>
                        </div>
                        @empty
                        <div class="text-center py-12 text-stone-400">
                            <svg class="mx-auto h-14 w-14 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="font-display font-semibold text-lg">Sin citas programadas</p>
                            <p class="text-sm mt-1">Reserva tu primera cita ahora</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Budgets -->
                <div class="bg-white rounded-[2rem] border border-teal-500/10 shadow-lg overflow-hidden">
                    <div class="px-8 py-5 border-b border-stone-100 bg-gradient-to-r from-emerald-50 to-transparent">
                        <h3 class="text-xl font-display font-bold text-stone-800 flex items-center gap-3">
                            <span class="bg-emerald-600 text-white p-2.5 rounded-xl shadow-md shadow-emerald-600/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            Presupuestos
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @forelse($patient->budgets()->orderBy('created_at', 'desc')->get() as $budget)
                        <a href="{{ URL::signedRoute('portal.budgets.view', ['budget' => $budget]) }}" class="block bg-[#FFFBF5] border border-stone-100 rounded-2xl p-5 hover:border-teal-200 hover:shadow-md transition-all duration-200 group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-200 transition-colors flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xl font-display font-bold text-emerald-700">${{ number_format($budget->total, 0, ',', '.') }}</p>
                                        <p class="text-xs text-stone-500 mt-0.5">Vence {{ $budget->expires_at?->isoFormat('D MMM Y') ?? 'sin fecha' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold gap-1.5 border
                                        @if($budget->status === 'accepted') bg-emerald-50 text-emerald-700 border-emerald-200
                                        @elseif($budget->status === 'sent') bg-amber-50 text-amber-700 border-amber-200
                                        @elseif($budget->status === 'rejected') bg-rose-50 text-rose-700 border-rose-200
                                        @else bg-stone-100 text-stone-600 border-stone-200 @endif">
                                        <span class="w-1.5 h-1.5 rounded-full @if($budget->status === 'sent') bg-amber-500 animate-pulse @else bg-current @endif"></span>
                                        @switch($budget->status) @case('accepted') Aceptado @break @case('sent') Enviado @break @case('rejected') Rechazado @break @case('draft') Borrador @break @default {{ $budget->status }} @endswitch
                                    </span>
                                    <svg class="w-5 h-5 text-stone-300 group-hover:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-12 text-stone-400">
                            <svg class="mx-auto h-14 w-14 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="font-display font-semibold text-lg">Sin presupuestos</p>
                            <p class="text-sm mt-1">Apareceran cuando tu clinica los envie</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
