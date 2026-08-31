<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal del Paciente') — {{ $patient->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800|work-sans:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --teal: #0D9488;
            --gold: #F59E0B;
            --ink: #292524;
        }
        body {
            font-family: 'Work Sans', sans-serif;
            background: #FFFBF5;
            color: var(--ink);
            overflow-x: hidden;
        }
        .font-display { font-family: 'Outfit', sans-serif; letter-spacing: -0.02em; }
    </style>
    <style>
        .portal-reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .portal-reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .portal-reveal-delay-1 { transition-delay: 0.1s; }
        .portal-reveal-delay-2 { transition-delay: 0.2s; }
        .portal-reveal-delay-3 { transition-delay: 0.3s; }
        @media (prefers-reduced-motion: reduce) {
            .portal-reveal { opacity: 1; transform: none; transition: none; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-cyan-50 via-white to-primary-50/50 font-sans antialiased min-h-screen">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
    </div>

    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-600 text-white px-4 py-2 rounded-md z-50">
        Saltar al contenido principal
    </a>

    <div class="relative min-h-screen">
        <!-- Header -->
        <header class="sticky top-0 z-40 bg-[#FFFBF5]/80 backdrop-blur-xl border-b border-teal-500/10">
            <div class="max-w-6xl mx-auto py-4 px-6 lg:px-12 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white text-lg font-bold shadow-lg shadow-primary-500/20">
                        {{ strtoupper(substr($patient->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-lg font-display font-bold text-stone-800">Hola, {{ $patient->name }}</h1>
                        <p class="text-xs text-stone-500">Portal del Paciente</p>
                    </div>
                </div>
                <span class="hidden sm:inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-full text-sm font-medium shadow-lg shadow-primary-500/20">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Dental Portal
                </span>
            </div>
        </header>

        <div class="max-w-6xl mx-auto py-6 px-6 lg:px-12 flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            <nav class="lg:w-56 flex-shrink-0" aria-label="Navegación del portal">
                <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden sticky top-24">
                    <div class="py-3 px-4 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50">
                        <p class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Menú</p>
                    </div>
                    <div class="p-3 space-y-1">
                        <a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-md shadow-primary-500/20 mb-2 transition-all duration-200 hover:from-primary-700 hover:to-primary-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Reservar Cita
                        </a>
                        <a href="{{ URL::signedRoute('portal.dashboard', ['patient' => $patient]) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('portal.dashboard') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Inicio
                        </a>
                        <a href="{{ URL::signedRoute('portal.appointments', ['patient' => $patient]) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('portal.appointments*') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Citas
                        </a>
                        <a href="{{ URL::signedRoute('portal.prescriptions', ['patient' => $patient]) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('portal.prescriptions*') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Recetas
                        </a>
                        <a href="{{ URL::signedRoute('portal.medical-history', ['patient' => $patient]) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('portal.medical-history') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Historia Clínica
                        </a>
                        <a href="{{ URL::signedRoute('portal.payments', ['patient' => $patient]) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('portal.payments') ? 'bg-primary-50 text-primary-700 shadow-sm' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Pagos
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main id="main-content" class="flex-1 min-w-0">
                @if (session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                @endif

                @if($patient->status === 'inactive')
                <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-6 py-4 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-medium">Tu tratamiento ha finalizado.</p>
                        <p class="text-sm">Puedes agendar una nueva cita en cualquier momento con el botón "Reservar Cita".</p>
                    </div>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reveals = document.querySelectorAll('.portal-reveal');
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                        }
                    });
                },
                { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
            );
            reveals.forEach(el => observer.observe(el));
        });
    </script>
    @stack('scripts')
</body>
</html>
