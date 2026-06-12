<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'DentalFlow') }} — Gestión Dental Inteligente</title>
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
            background: var(--cream);
            color: var(--ink);
            overflow-x: hidden;
        }

        .font-display {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
        }

        /* ===== HERO ORBIT ANIMATION ===== */
        @keyframes orbit {
            0% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(15%, -8%) rotate(2deg); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }
        @keyframes orbit-reverse {
            0% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-10%, 5%) rotate(-1.5deg); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }
        .animate-orbit { animation: orbit 16s ease-in-out infinite; }
        .animate-orbit-reverse { animation: orbit-reverse 20s ease-in-out infinite; }

        /* ===== FLOATING ANIMATION ===== */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-16px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 6s ease-in-out 2s infinite; }
        .animate-float-slow { animation: float 8s ease-in-out 4s infinite; }

        /* ===== SCROLL REVEAL ===== */
        @keyframes reveal {
            from { opacity: 0; transform: translateY(32px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .reveal {
            opacity: 0;
            animation: reveal 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .reveal-delay-1 { animation-delay: 0.1s; }
        .reveal-delay-2 { animation-delay: 0.2s; }
        .reveal-delay-3 { animation-delay: 0.3s; }
        .reveal-delay-4 { animation-delay: 0.4s; }
        .reveal-delay-5 { animation-delay: 0.5s; }

        /* ===== COUNTER ===== */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .count-up { animation: countUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* ===== GLOW PULSE ===== */
        @keyframes glow-pulse {
            0%, 100% { box-shadow: 0 0 40px rgba(13, 148, 136, 0.15); }
            50% { box-shadow: 0 0 80px rgba(13, 148, 136, 0.25); }
        }
        .glow-pulse { animation: glow-pulse 3s ease-in-out infinite; }

        /* ===== WAVE DIVIDER ===== */
        .wave-divider {
            position: relative;
        }
        .wave-divider::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            right: 0;
            height: 60px;
            background: var(--cream);
            border-radius: 0 0 100% 100%;
            z-index: 1;
        }
        .wave-divider-bottom {
            position: relative;
        }
        .wave-divider-bottom::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 60px;
            background: var(--cream);
            border-radius: 100% 100% 0 0;
            z-index: 1;
        }

        /* ===== CARD HOVER ===== */
        .feature-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 48px -12px rgba(13, 148, 136, 0.15);
        }
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(-5deg);
            background: var(--teal);
            color: white;
        }
        .feature-icon {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* ===== TOOTH SVG ANIMATION ===== */
        @keyframes tooth-appear {
            from { transform: scale(0) rotate(-30deg); opacity: 0; }
            to { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        @keyframes smile-glow {
            0%, 100% { filter: drop-shadow(0 0 8px rgba(245, 158, 11, 0.3)); }
            50% { filter: drop-shadow(0 0 20px rgba(245, 158, 11, 0.6)); }
        }

        /* ===== REDUCED MOTION ===== */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body class="antialiased">

    <!-- ===== NAVIGATION ===== -->
    <nav class="fixed top-0 inset-x-0 z-50 bg-[#FFFBF5]/80 backdrop-blur-xl border-b border-teal-500/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-4 flex justify-between items-center">
            <a href="/" class="flex items-center gap-3 text-2xl font-display font-bold text-teal-700 hover:text-teal-600 transition-colors">
                <span class="bg-teal-600 text-white rounded-xl p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </span>
                DentalFlow
            </a>
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                    <a href="{{ url('/app') }}" class="font-medium text-stone-500 hover:text-teal-700 transition-colors duration-200">Escritorio</a>
                @else
                    <a href="{{ route('login') }}" class="font-medium text-stone-500 hover:text-teal-700 transition-colors duration-200">Ingresar</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all duration-200 shadow-lg shadow-teal-600/20 hover:shadow-teal-600/30">
                                Comenzar Gratis
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- ===== HERO ===== -->
    <section class="relative min-h-screen flex items-center pt-20 overflow-hidden">
        <!-- Ambient orbs -->
        <div class="absolute top-1/4 -left-32 w-[600px] h-[600px] bg-teal-400/10 rounded-full blur-3xl animate-orbit pointer-events-none"></div>
        <div class="absolute bottom-1/4 -right-32 w-[500px] h-[500px] bg-gold-light/40 rounded-full blur-3xl animate-orbit-reverse pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/3 w-[300px] h-[300px] bg-orange-500/5 rounded-full blur-2xl animate-orbit pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-20 grid lg:grid-cols-2 gap-16 items-center relative z-10">
            <!-- Left: Text -->
            <div>
                <div class="inline-flex items-center gap-2 py-2 px-4 rounded-full bg-teal-50 text-teal-700 text-sm font-semibold mb-8 border border-teal-100 reveal">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    Disponible ahora — v2.0
                </div>

                <h1 class="text-5xl lg:text-7xl font-display font-bold leading-[1.05] mb-6 reveal reveal-delay-1">
                    Tu clínica dental,<br>
                    <span class="bg-gradient-to-r from-teal-600 via-teal-500 to-gold bg-clip-text text-transparent">sin papeles.</span>
                </h1>

                <p class="text-lg text-stone-500 leading-relaxed mb-8 max-w-lg reveal reveal-delay-2">
                    La plataforma todo-en-uno para profesionales dentales modernos. 
                    Fichas clínicas, agenda inteligente, presupuestos y cobranza en un solo lugar.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 reveal reveal-delay-3">
                    <a href="{{ route('register') }}" class="group bg-teal-600 text-white font-semibold py-4 px-8 rounded-xl text-center hover:bg-teal-700 transition-all duration-300 shadow-xl shadow-teal-600/25 hover:shadow-teal-600/40 hover:-translate-y-0.5">
                        Prueba gratuita de 30 días
                        <span class="inline-block ml-2 group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                    <a href="#features" class="bg-white text-stone-500 border border-stone-200 font-semibold py-4 px-8 rounded-xl text-center hover:bg-stone-50 hover:border-stone-300 transition-all duration-200">
                        Ver funcionalidades ↓
                    </a>
                </div>

                <div class="flex items-center gap-8 mt-12 text-sm text-stone-500 reveal reveal-delay-4">
                    <div class="flex items-center gap-2">
                        <svg class="size-5 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Sin tarjeta de crédito
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="size-5 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Configuración en 5 minutos
                    </div>
                </div>
            </div>

            <!-- Right: Visual -->
            <div class="relative reveal reveal-delay-2">
                <!-- Main card mockup -->
                <div class="relative bg-white rounded-3xl shadow-2xl border border-stone-100 p-8 overflow-hidden glow-pulse">
                    <!-- Decorative top bar -->
                    <div class="flex items-center gap-2 mb-6">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                        <span class="w-3 h-3 rounded-full bg-green-400"></span>
                        <span class="ml-3 text-xs text-stone-400 font-medium">DentalFlow — Escritorio</span>
                    </div>

                    <!-- Mini dashboard -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-teal-50 rounded-xl p-3">
                                <div class="text-xs text-teal-600 font-semibold mb-1">Pacientes</div>
                                <div class="text-2xl font-display font-bold text-teal-700">248</div>
                            </div>
                            <div class="bg-amber-50 rounded-xl p-3">
                                <div class="text-xs text-amber-600 font-semibold mb-1">Citas Hoy</div>
                                <div class="text-2xl font-display font-bold text-amber-700">12</div>
                            </div>
                            <div class="bg-emerald-50 rounded-xl p-3">
                                <div class="text-xs text-emerald-600 font-semibold mb-1">Ingresos</div>
                                <div class="text-2xl font-display font-bold text-emerald-700">$1.2M</div>
                            </div>
                        </div>
                        <div class="bg-stone-50 rounded-xl p-4">
                            <div class="h-24 flex items-end gap-2">
                                <div class="w-1/6 bg-teal-400 rounded-t h-[40%]"></div>
                                <div class="w-1/6 bg-teal-400 rounded-t h-[60%]"></div>
                                <div class="w-1/6 bg-teal-400 rounded-t h-[30%]"></div>
                                <div class="w-1/6 bg-teal-400 rounded-t h-[80%]"></div>
                                <div class="w-1/6 bg-teal-400 rounded-t h-[50%]"></div>
                                <div class="w-1/6 bg-teal-400 rounded-t h-[90%]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating tooth icon -->
                <div class="absolute -top-6 -right-6 w-20 h-20 bg-gradient-to-br from-gold to-amber-500 rounded-2xl flex items-center justify-center shadow-xl shadow-amber-500/30 animate-float" style="animation:smile-glow 3s ease-in-out infinite;">
                    <svg class="size-10 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                    </svg>
                </div>

                <!-- Small floating card -->
                <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-lg border border-stone-100 p-3 flex items-center gap-3 animate-float-delayed">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-stone-400">Última cita</div>
                        <div class="text-sm font-semibold text-stone-700">María González</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FEATURES ===== -->
    <section id="features" class="py-24 bg-white wave-divider wave-divider-bottom">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="text-teal-600 font-semibold text-sm tracking-wide uppercase">Funcionalidades</span>
                <h2 class="text-4xl lg:text-5xl font-display font-bold mt-3 mb-5">Todo lo que necesitas para<br>administrar tu consulta</h2>
                <p class="text-stone-500 text-lg">Enfócate en tus pacientes. Nosotros nos encargamos de la administración.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card p-10 rounded-3xl bg-stone-50 border border-stone-100 cursor-pointer group">
                    <div class="feature-icon w-14 h-14 bg-white rounded-2xl shadow-sm text-teal-500 flex items-center justify-center mb-8 border border-stone-100">
                        <svg class="size-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold mb-3 text-stone-800">Fichas de Pacientes</h3>
                    <p class="text-stone-500 leading-relaxed">Historial clínico digital, alergias, tratamientos y notas. Accesible desde cualquier dispositivo, siempre seguro.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card p-10 rounded-3xl bg-stone-50 border border-stone-100 cursor-pointer group">
                    <div class="feature-icon w-14 h-14 bg-white rounded-2xl shadow-sm text-teal-500 flex items-center justify-center mb-8 border border-stone-100">
                        <svg class="size-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0h18M5.25 12h13.5h-13.5Zm1 5.25h13.5h-13.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold mb-3 text-stone-800">Agenda Inteligente</h3>
                    <p class="text-stone-500 leading-relaxed">Calendario drag & drop con confirmaciones automáticas. Evita sobreposiciones y reduce las ausencias.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card p-10 rounded-3xl bg-stone-50 border border-stone-100 cursor-pointer group">
                    <div class="feature-icon w-14 h-14 bg-white rounded-2xl shadow-sm text-teal-500 flex items-center justify-center mb-8 border border-stone-100">
                        <svg class="size-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold mb-3 text-stone-800">Presupuestos y Cobros</h3>
                    <p class="text-stone-500 leading-relaxed">Genera presupuestos desde el odontograma. Controla pagos pendientes y envía recordatorios automáticos.</p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card p-10 rounded-3xl bg-stone-50 border border-stone-100 cursor-pointer group">
                    <div class="feature-icon w-14 h-14 bg-white rounded-2xl shadow-sm text-teal-500 flex items-center justify-center mb-8 border border-stone-100">
                        <svg class="size-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold mb-3 text-stone-800">Panel de Control</h3>
                    <p class="text-stone-500 leading-relaxed">KPIs en tiempo real: ingresos, citas, inventario. Gráficos interactivos que te ayudan a decidir mejor.</p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card p-10 rounded-3xl bg-stone-50 border border-stone-100 cursor-pointer group">
                    <div class="feature-icon w-14 h-14 bg-white rounded-2xl shadow-sm text-teal-500 flex items-center justify-center mb-8 border border-stone-100">
                        <svg class="size-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold mb-3 text-stone-800">Odontograma Interactivo</h3>
                    <p class="text-stone-500 leading-relaxed">Planificación visual de tratamientos con odontograma SVG de 32 piezas. Diagnósticos a un clic.</p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card p-10 rounded-3xl bg-stone-50 border border-stone-100 cursor-pointer group">
                    <div class="feature-icon w-14 h-14 bg-white rounded-2xl shadow-sm text-teal-500 flex items-center justify-center mb-8 border border-stone-100">
                        <svg class="size-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold mb-3 text-stone-800">Multiclínica</h3>
                    <p class="text-stone-500 leading-relaxed">Administra múltiples sucursales desde un solo lugar. Cada clínica con sus propios datos, usuarios y configuración.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== STATS BAR ===== -->
    <section class="py-16 bg-gradient-to-r from-teal-600 via-teal-500 to-emerald-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDE4YzEuNjU3IDAgMy0xLjM0MyAzLTNzLTEuMzQzLTMtMy0zLTMgMS4zNDMtMyAzIDEuMzQzIDMgMyAzem0tNiAwYzEuNjU3IDAgMy0xLjM0MyAzLTNzLTEuMzQzLTMtMy0zLTMgMS4zNDMtMyAzIDEuMzQzIDMgMyAzeiIvPjwvZz48L2c+PC9zdmc+')] opacity-30"></div>
        <div class="max-w-5xl mx-auto px-6 lg:px-12 grid grid-cols-2 md:grid-cols-4 gap-8 text-center relative z-10">
            <div>
                <div class="text-4xl lg:text-5xl font-display font-bold text-white mb-1 count-up">500+</div>
                <div class="text-teal-200 text-sm font-medium">Clínicas activas</div>
            </div>
            <div>
                <div class="text-4xl lg:text-5xl font-display font-bold text-white mb-1 count-up">50k+</div>
                <div class="text-teal-200 text-sm font-medium">Pacientes registrados</div>
            </div>
            <div>
                <div class="text-4xl lg:text-5xl font-display font-bold text-white mb-1 count-up">99.9%</div>
                <div class="text-teal-200 text-sm font-medium">Uptime garantizado</div>
            </div>
            <div>
                <div class="text-4xl lg:text-5xl font-display font-bold text-white mb-1 count-up">24/7</div>
                <div class="text-teal-200 text-sm font-medium">Soporte dedicado</div>
            </div>
        </div>
    </section>

    <!-- ===== HOW IT WORKS ===== -->
    <section class="py-24 bg-[#FFFBF5]">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="text-teal-600 font-semibold text-sm tracking-wide uppercase">Comenzar es fácil</span>
                <h2 class="text-4xl lg:text-5xl font-display font-bold mt-3 mb-5">Tu clínica en línea<br>en menos de 5 minutos</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center mx-auto mb-5 text-2xl font-display font-bold">1</div>
                    <h3 class="text-lg font-display font-bold mb-2">Crea tu cuenta</h3>
                    <p class="text-stone-500 text-sm leading-relaxed">Registra tu clínica en segundos. Nombre, email y contraseña.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-5 text-2xl font-display font-bold">2</div>
                    <h3 class="text-lg font-display font-bold mb-2">Invita a tu equipo</h3>
                    <p class="text-stone-500 text-sm leading-relaxed">Agrega doctores, asistentes y administradores. Cada uno con sus permisos.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-5 text-2xl font-display font-bold">3</div>
                    <h3 class="text-lg font-display font-bold mb-2">Empieza a trabajar</h3>
                    <p class="text-stone-500 text-sm leading-relaxed">Carga tus pacientes, agenda citas y gestiona todo desde un solo lugar.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="py-24 bg-white">
        <div class="max-w-5xl mx-auto px-6 lg:px-12">
            <div class="relative bg-gradient-to-br from-teal-700 via-teal-600 to-emerald-700 rounded-[2.5rem] p-16 lg:p-24 text-center text-white overflow-hidden">
                <!-- Abstract shapes -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-amber-500/20 rounded-full translate-y-1/3 -translate-x-1/3"></div>
                <svg class="absolute top-10 left-10 size-20 text-white/5 animate-float-slow" fill="currentColor" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>

                <div class="relative z-10">
                    <h2 class="text-4xl lg:text-5xl font-display font-bold mb-6">¿Listo para modernizar tu clínica?</h2>
                    <p class="text-teal-200 text-lg mb-10 max-w-xl mx-auto leading-relaxed">
                        Únete a cientos de dentistas que ya confían en DentalFlow. 
                        Sin compromiso, sin tarjeta de crédito.
                    </p>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-teal-700 font-semibold py-4 px-10 rounded-xl hover:bg-teal-50 transition-all duration-200 shadow-xl hover:shadow-2xl hover:-translate-y-0.5 group">
                        Comenzar ahora
                        <svg class="size-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-stone-50 border-t border-stone-200 py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid md:grid-cols-3 gap-10 mb-12">
                <div>
                    <div class="flex items-center gap-2 text-xl font-display font-bold text-teal-700 mb-4">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                        DentalFlow
                    </div>
                    <p class="text-stone-500 text-sm leading-relaxed">Software de gestión dental todo-en-uno para clínicas modernas.</p>
                </div>
                <div>
                    <h4 class="font-display font-semibold text-stone-700 mb-4">Producto</h4>
                    <ul class="space-y-2 text-sm text-stone-500">
                        <li><a href="#features" class="hover:text-teal-600 transition-colors">Funcionalidades</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-teal-600 transition-colors">Registrarse</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-teal-600 transition-colors">Ingresar</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display font-semibold text-stone-700 mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm text-stone-500">
                        <li><a href="/terms" class="hover:text-teal-600 transition-colors">Terminos de Servicio</a></li>
                        <li><a href="/privacy" class="hover:text-teal-600 transition-colors">Politica de Privacidad</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-stone-200 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-stone-500">
                <span>&copy; {{ date('Y') }} DentalFlow. Todos los derechos reservados.</span>
                <span>Hecho con ❤️ para dentistas</span>
            </div>
        </div>
    </footer>

    <!-- ===== SCROLL REVEAL OBSERVER ===== -->
    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal').forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });
    </script>
</body>
</html>
