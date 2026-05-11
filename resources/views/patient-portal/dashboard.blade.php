<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Portal - {{ $patient->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        .animate-blob {
            animation: blob 10s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
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
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                        Hola, {{ $patient->name }} 👋
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Bienvenido a tu portal de paciente</p>
                </div>
                <span class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-full text-sm font-medium shadow-lg shadow-blue-200">
                    <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Dental Portal
                </span>
            </div>
        </header>

        <main id="main-content" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex justify-end">
                <a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}"
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105">
                    <!-- Heroicon name: solid/calendar -->
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                    </svg>
                    Reservar Cita
                </a>
            </div>
            <!-- Flash Messages -->
            @if (session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl shadow-md"
                role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Próximas Citas Section -->
                <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-100 hover:shadow-2xl transition-all duration-300">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <span class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white p-2.5 rounded-xl mr-3 shadow-lg shadow-blue-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </span>
                                Próximas Citas
                            </h3>
                        </div>
                    </div>

                    <div class="p-6 bg-gradient-to-b from-gray-50/50 to-white">
                        <div class="space-y-4">
                            @forelse($patient->appointments as $appointment)
                            <div
                                class="bg-white border border-gray-100 rounded-2xl shadow-lg shadow-gray-100/50 hover:shadow-xl hover:shadow-blue-100/50 transition-all duration-300 p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center group cursor-pointer transform hover:-translate-y-1">
                                <div class="flex items-start space-x-4">
                                    <div
                                        class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-cyan-500 text-white rounded-2xl p-3 group-hover:from-blue-600 group-hover:to-cyan-600 transition-all duration-300 shadow-lg shadow-blue-200">
                                        <!-- Dental Tooth Icon -->
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                            {{ $appointment->procedurePrice->procedure_name ?? 'Consulta General' }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $appointment->start_time->format('d M, Y') }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $appointment->start_time->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0">
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-4 text-gray-500 text-lg">No tienes citas programadas</p>
                                <p class="mt-2 text-gray-400 text-sm">¡Reserva tu primera cita ahora!</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                                    <div>
                                        <h4
                                            class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                            {{ ucfirst($appointment->type) }}
                                        </h4>

                                        <div class="flex items-center space-x-4 mt-2">
                                            <div class="flex items-center text-sm text-gray-500">
                                                <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $appointment->start_time->format('d M, Y') }}
                                            </div>
                                            <div class="flex items-center text-sm text-gray-500">
                                                <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $appointment->start_time->format('h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0 flex items-center">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-700 border border-green-200' : 
                                            ($appointment->status === 'cancelled' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200') }}">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5 
                                                {{ $appointment->status === 'confirmed' ? 'bg-green-500' : 
                                                ($appointment->status === 'cancelled' ? 'bg-red-500' : 'bg-yellow-500') }}">
                                        </span>
                                        {{ ucfirst($appointment->status === 'confirmed' ? 'Confirmada' :
                                        ($appointment->status === 'cancelled' ? 'Cancelada' : 'Pendiente')) }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-4">
                                    <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">No tienes citas próximas</h3>
                                <p class="mt-1 text-sm text-gray-500">Programa tu próxima visita ahora.</p>
                                <div class="mt-6">
                                    <a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Reservar Cita
                                    </a>
                                </div>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                                Presupuestos
                            </h3>
                        </div>
                    </div>

                    <div class="p-6 bg-gradient-to-b from-gray-50/50 to-white">
                        <ul class="space-y-4">
                            @forelse($patient->budgets as $budget)
                            <li class="bg-white border border-gray-100 rounded-2xl shadow-lg shadow-gray-100/50 hover:shadow-xl hover:shadow-emerald-100/50 transition-all duration-300 p-5 group cursor-pointer transform hover:-translate-y-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-teal-500 text-white rounded-2xl p-3 shadow-lg shadow-emerald-200 group-hover:from-emerald-600 group-hover:to-teal-600 transition-all duration-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                                ${{ number_format($budget->total, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Vence: {{ $budget->expires_at?->format('d M Y') ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span
                                            class="px-4 py-2 rounded-full text-xs font-semibold shadow-lg
                                                                {{ $budget->status === 'accepted' ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 shadow-green-200' : 
                                                                ($budget->status === 'sent' ? 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-700 shadow-yellow-200' : 'bg-gradient-to-r from-gray-100 to-slate-100 text-gray-700 shadow-gray-200') }}">
                                            <span class="w-2 h-2 rounded-full mr-2 inline-block
                                                {{ $budget->status === 'accepted' ? 'bg-green-500' : 
                                                ($budget->status === 'sent' ? 'bg-yellow-500 animate-pulse' : 'bg-gray-500') }}">
                                            </span>
                                            {{ ucfirst($budget->status) }}
                                        </span>
                                        @if($budget->status === 'sent')
                                        <form
                                            action="{{ URL::signedRoute('portal.budgets.accept', ['budget' => $budget]) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-lg shadow-emerald-200 transition-all duration-200 transform hover:scale-105">
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
                                    <svg class="h-8 w-8 text-emerald-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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