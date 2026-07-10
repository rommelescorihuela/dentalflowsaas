<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'DentalFlow') }} — Gestión Dental Profesional</title>
    <meta name="description" content="Plataforma SaaS para gestión de clínicas dentales en Venezuela. Odontograma interactivo, historias clínicas, facturación y más.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-serif:400,400i|dm-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .reveal { opacity: 0; transform: translateY(24px); transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.12s; }
        .reveal-delay-2 { transition-delay: 0.24s; }
        .reveal-delay-3 { transition-delay: 0.36s; }
        .reveal-delay-4 { transition-delay: 0.48s; }
    </style>
</head>

<body class="antialiased bg-[#faf8f5] text-[#1a1b2f] selection:bg-[#c75b4a] selection:text-white font-sans">

    <div x-data="{ mobileOpen: false, scrolled: false }" x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)" class="relative">

        <!-- ===== NAVBAR ===== -->
        <nav x-bind:class="scrolled ? 'bg-[#faf8f5]/95 backdrop-blur-lg shadow-lg border-b border-[#e8e0d6]' : 'bg-transparent'" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="flex items-center justify-between h-20">
                    <a href="/" class="flex items-center gap-3 group">
                        <span class="relative flex items-center justify-center w-10 h-10">
                            <span class="absolute inset-0 bg-[#c75b4a] rounded-xl rotate-6 group-hover:rotate-12 transition-transform duration-300"></span>
                            <span class="absolute inset-0 bg-[#1a1b2f] rounded-xl -rotate-3 group-hover:rotate-0 transition-transform duration-300"></span>
                            <span class="relative text-white font-bold text-sm tracking-tight z-10">DF</span>
                        </span>
                        <span class="text-lg font-bold text-[#1a1b2f] tracking-tight">DentalFlow</span>
                    </a>

                    <div class="hidden md:flex items-center gap-1">
                        <a href="#features" class="px-4 py-2 text-sm font-medium text-[#6b6358] hover:text-[#c75b4a] rounded-lg hover:bg-[#c75b4a]/5 transition-all duration-200">Funciones</a>
                        <a href="#pricing" class="px-4 py-2 text-sm font-medium text-[#6b6358] hover:text-[#c75b4a] rounded-lg hover:bg-[#c75b4a]/5 transition-all duration-200">Planes</a>
                        <a href="#testimonials" class="px-4 py-2 text-sm font-medium text-[#6b6358] hover:text-[#c75b4a] rounded-lg hover:bg-[#c75b4a]/5 transition-all duration-200">Testimonios</a>
                        <a href="#faq" class="px-4 py-2 text-sm font-medium text-[#6b6358] hover:text-[#c75b4a] rounded-lg hover:bg-[#c75b4a]/5 transition-all duration-200">FAQ</a>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="hidden sm:inline-flex items-center px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-[#c75b4a] hover:bg-[#b04a3a] shadow-lg shadow-[#c75b4a]/20 transition-all duration-200">
                                    Panel
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-[#6b6358] hover:text-[#c75b4a] transition-colors">Entrar</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-[#1a1b2f] hover:bg-[#2a2b3f] shadow-lg shadow-[#1a1b2f]/20 transition-all duration-200">
                                        Empezar gratis
                                    </a>
                                @endif
                            @endauth
                        @endif
                        <button x-on:click="mobileOpen = !mobileOpen" class="md:hidden p-2.5 rounded-lg text-[#6b6358] hover:text-[#c75b4a] hover:bg-[#c75b4a]/5 transition-all" aria-label="Menu">
                            <svg x-show="!mobileOpen" class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                            <svg x-show="mobileOpen" class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-3" class="md:hidden border-t border-[#e8e0d6] bg-[#faf8f5] shadow-xl">
                <div class="px-6 py-5 space-y-1">
                    <a href="#features" class="block px-3 py-3 rounded-lg text-[#6b6358] font-medium hover:text-[#c75b4a] hover:bg-[#c75b4a]/5 transition-all">Funciones</a>
                    <a href="#pricing" class="block px-3 py-3 rounded-lg text-[#6b6358] font-medium hover:text-[#c75b4a] hover:bg-[#c75b4a]/5 transition-all">Planes</a>
                    <a href="#testimonials" class="block px-3 py-3 rounded-lg text-[#6b6358] font-medium hover:text-[#c75b4a] hover:bg-[#c75b4a]/5 transition-all">Testimonios</a>
                    <a href="#faq" class="block px-3 py-3 rounded-lg text-[#6b6358] font-medium hover:text-[#c75b4a] hover:bg-[#c75b4a]/5 transition-all">FAQ</a>
                    <div class="border-t border-[#e8e0d6] pt-4 mt-3 space-y-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="block w-full text-center px-5 py-3 rounded-lg text-sm font-semibold text-white bg-[#c75b4a]">Panel</a>
                            @else
                                <a href="{{ route('login') }}" class="block w-full text-center px-5 py-3 rounded-lg text-sm font-semibold text-[#1a1b2f] bg-white border border-[#e8e0d6]">Entrar</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="block w-full text-center px-5 py-3 rounded-lg text-sm font-semibold text-white bg-[#1a1b2f]">Empezar gratis</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- ===== HERO ===== -->
        <section class="relative min-h-[85vh] flex items-center pt-28 lg:pt-0 overflow-hidden">
            <!-- Textured background -->
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1200px] h-[800px] bg-gradient-to-b from-[#c75b4a]/8 to-transparent rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-[#1a1b2f]/3 rounded-full blur-3xl"></div>
                <!-- Decorative dots pattern -->
                <div class="absolute top-24 right-12 grid grid-cols-6 gap-2 opacity-20">
                    <span class="w-2 h-2 rounded-full bg-[#c75b4a]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#1a1b2f]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#c75b4a]/60"></span>
                    <span class="w-2 h-2 rounded-full bg-[#c75b4a]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#1a1b2f]/60"></span>
                    <span class="w-2 h-2 rounded-full bg-[#c75b4a]/60"></span>
                    <span class="w-2 h-2 rounded-full bg-[#1a1b2f]/60"></span>
                    <span class="w-2 h-2 rounded-full bg-[#c75b4a]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#1a1b2f]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#c75b4a]/60"></span>
                    <span class="w-2 h-2 rounded-full bg-[#c75b4a]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#1a1b2f]/60"></span>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-12 w-full relative z-10">
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                    <div class="lg:col-span-6 pt-8 lg:pt-0">
                        <p class="text-sm font-semibold text-[#c75b4a] uppercase tracking-[0.2em] mb-6 reveal visible">Plataforma todo-en-uno</p>

                        <h1 class="font-serif text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-normal leading-[1.08] tracking-tight mb-8 reveal visible">
                            <span class="text-[#1a1b2f]">Gestiona tu</span><br>
                            <span class="text-[#1a1b2f]">clínica dental</span><br>
                            <span class="italic text-[#c75b4a]">con estilo.</span>
                        </h1>

                        <p class="text-lg text-[#6b6358] leading-relaxed max-w-lg mb-10 reveal visible reveal-delay-1">
                            La plataforma que transforma la gestión de tu consultorio. Odontograma digital, historias clínicas, facturación — todo en un solo lugar, diseñado para Venezuela.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 reveal visible reveal-delay-2">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-lg text-base font-semibold text-white bg-[#c75b4a] hover:bg-[#b04a3a] shadow-xl shadow-[#c75b4a]/20 hover:-translate-y-0.5 transition-all duration-200">
                                Comenzar prueba gratis
                                <svg class="ml-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="#features" class="inline-flex items-center justify-center px-8 py-4 rounded-lg text-base font-semibold text-[#1a1b2f] bg-white border border-[#e8e0d6] hover:border-[#c75b4a]/30 hover:bg-[#c75b4a]/5 shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Ver demo
                            </a>
                        </div>

                        <div class="flex items-center gap-6 mt-12 reveal visible reveal-delay-3">
                            <div class="flex -space-x-3">
                                <div class="w-10 h-10 rounded-full border-2 border-[#faf8f5] bg-gradient-to-br from-[#c75b4a] to-[#d45d4a] flex items-center justify-center text-white text-xs font-bold shadow-md">DR</div>
                                <div class="w-10 h-10 rounded-full border-2 border-[#faf8f5] bg-gradient-to-br from-[#1a1b2f] to-[#2a2b3f] flex items-center justify-center text-white text-xs font-bold shadow-md">SM</div>
                                <div class="w-10 h-10 rounded-full border-2 border-[#faf8f5] bg-gradient-to-br from-amber-600 to-amber-700 flex items-center justify-center text-white text-xs font-bold shadow-md">CL</div>
                                <div class="w-10 h-10 rounded-full border-2 border-[#faf8f5] bg-[#6b6358] flex items-center justify-center text-white text-xs font-bold shadow-md">+2k</div>
                            </div>
                            <div class="text-sm text-[#8a8072]">
                                <span class="font-semibold text-[#1a1b2f]">2,000+</span> clínicas nos respaldan
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-6 relative reveal visible reveal-delay-2">
                        <div class="relative bg-white rounded-2xl border border-[#e8e0d6] shadow-2xl shadow-[#c75b4a]/10 overflow-hidden">
                            <!-- Mac window dots -->
                            <div class="px-5 py-4 border-b border-[#e8e0d6] flex items-center gap-2.5 bg-[#f8f6f2]">
                                <span class="w-3 h-3 rounded-full bg-[#e8d5c8]"></span>
                                <span class="w-3 h-3 rounded-full bg-[#d4c8b8]"></span>
                                <span class="w-3 h-3 rounded-full bg-[#c4b8a8]"></span>
                                <span class="ml-2 text-xs text-[#8a8072] font-medium">Vista previa del panel</span>
                            </div>
                            <div class="p-6 sm:p-8">
                                <!-- Dashboard preview -->
                                <div class="flex items-center justify-between mb-8">
                                    <div>
                                        <p class="text-xs text-[#8a8072] uppercase tracking-wider font-semibold">Citas hoy</p>
                                        <p class="text-4xl font-serif text-[#1a1b2f] mt-1">12</p>
                                    </div>
                                    <span class="px-3 py-1.5 rounded-lg bg-[#c75b4a]/10 text-[#c75b4a] text-xs font-semibold flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#c75b4a]"></span>
                                        3 próximas
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-4 mb-8">
                                    <div class="bg-[#f8f6f2] rounded-xl p-5">
                                        <div class="w-10 h-10 rounded-lg bg-[#c75b4a]/10 text-[#c75b4a] flex items-center justify-center mb-3">
                                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        <p class="text-xs text-[#8a8072]">Pacientes</p>
                                        <p class="text-2xl font-bold text-[#1a1b2f]">1,284</p>
                                    </div>
                                    <div class="bg-[#f8f6f2] rounded-xl p-5">
                                        <div class="w-10 h-10 rounded-lg bg-[#1a1b2f]/10 text-[#1a1b2f] flex items-center justify-center mb-3">
                                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="text-xs text-[#8a8072]">Presupuestos</p>
                                        <p class="text-2xl font-bold text-[#1a1b2f]">Bs 48.2k</p>
                                    </div>
                                    <div class="bg-[#f8f6f2] rounded-xl p-5">
                                        <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center mb-3">
                                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        </div>
                                        <p class="text-xs text-[#8a8072]">Ingresos</p>
                                        <p class="text-2xl font-bold text-[#1a1b2f]">Bs 12.4k</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6 text-sm border-t border-[#e8e0d6] pt-5">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-1.5 h-6 rounded-full bg-[#c75b4a]"></span>
                                        <span class="text-[#6b6358]">Programadas</span>
                                        <span class="font-semibold text-[#1a1b2f]">8</span>
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-1.5 h-6 rounded-full bg-[#1a1b2f]"></span>
                                        <span class="text-[#6b6358]">Completadas</span>
                                        <span class="font-semibold text-[#1a1b2f]">4</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Floating elements -->
                        <div class="absolute -bottom-5 -right-5 w-24 h-24 bg-[#c75b4a]/15 rounded-2xl -z-10 blur-xl"></div>
                        <div class="absolute -top-5 -left-5 w-20 h-20 bg-[#1a1b2f]/10 rounded-2xl -z-10 blur-xl"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== TRUST BAR ===== -->
        <section class="py-16 bg-white border-y border-[#e8e0d6]">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <p class="text-center text-xs font-semibold text-[#8a8072] uppercase tracking-[0.2em] mb-10">Usado por clínicas en toda Venezuela</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 lg:gap-12">
                    <div class="text-center">
                        <p class="text-5xl font-serif text-[#1a1b2f]">2k+</p>
                        <p class="text-sm text-[#8a8072] mt-2">Clínicas activas</p>
                    </div>
                    <div class="text-center">
                        <p class="text-5xl font-serif text-[#1a1b2f]">50k+</p>
                        <p class="text-sm text-[#8a8072] mt-2">Pacientes registrados</p>
                    </div>
                    <div class="text-center">
                        <p class="text-5xl font-serif text-[#1a1b2f]">150k+</p>
                        <p class="text-sm text-[#8a8072] mt-2">Citas gestionadas</p>
                    </div>
                    <div class="text-center">
                        <p class="text-5xl font-serif text-[#1a1b2f]">99.9%</p>
                        <p class="text-sm text-[#8a8072] mt-2">Disponibilidad</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== FEATURES ===== -->
        <section id="features" class="py-24 lg:py-32">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="max-w-2xl mb-16 lg:mb-20 reveal">
                    <p class="text-sm font-semibold text-[#c75b4a] uppercase tracking-[0.2em] mb-5">Funcionalidades</p>
                    <h2 class="text-4xl lg:text-5xl font-serif text-[#1a1b2f] leading-[1.1] mb-6">Todo lo que necesitas para tu clínica</h2>
                    <p class="text-lg text-[#6b6358] leading-relaxed">Una plataforma unificada, diseñada para el odontólogo venezolano. Sin complicaciones.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl border border-[#e8e0d6] p-8 hover:border-[#c75b4a]/20 hover:shadow-lg hover:shadow-[#c75b4a]/5 transition-all duration-300 reveal reveal-delay-1">
                        <div class="w-12 h-12 rounded-xl bg-[#c75b4a]/10 text-[#c75b4a] flex items-center justify-center mb-5">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-3">Historias Clínicas</h3>
                        <p class="text-[#6b6358] leading-relaxed text-sm">Expedientes digitales seguros con historial médico, alergias y planes de tratamiento. Accesibles desde cualquier lugar.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-[#e8e0d6] p-8 hover:border-[#c75b4a]/20 hover:shadow-lg hover:shadow-[#c75b4a]/5 transition-all duration-300 reveal reveal-delay-2">
                        <div class="w-12 h-12 rounded-xl bg-[#c75b4a]/10 text-[#c75b4a] flex items-center justify-center mb-5">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-3">Odontograma Interactivo</h3>
                        <p class="text-[#6b6358] leading-relaxed text-sm">Plan de tratamiento visual con diagrama dental avanzado. Diagnostica y genera presupuestos al instante.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-[#e8e0d6] p-8 hover:border-[#c75b4a]/20 hover:shadow-lg hover:shadow-[#c75b4a]/5 transition-all duration-300 reveal reveal-delay-3">
                        <div class="w-12 h-12 rounded-xl bg-[#c75b4a]/10 text-[#c75b4a] flex items-center justify-center mb-5">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0h18M5.25 12h13.5M5.25 15h13.5M5.25 18h13.5"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-3">Agenda Inteligente</h3>
                        <p class="text-[#6b6358] leading-relaxed text-sm">Agendamiento con recordatorios automáticos, sincronización de calendario y disponibilidad en tiempo real.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-[#e8e0d6] p-8 hover:border-[#c75b4a]/20 hover:shadow-lg hover:shadow-[#c75b4a]/5 transition-all duration-300 reveal reveal-delay-1">
                        <div class="w-12 h-12 rounded-xl bg-[#c75b4a]/10 text-[#c75b4a] flex items-center justify-center mb-5">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-3">Facturación y Pagos</h3>
                        <p class="text-[#6b6358] leading-relaxed text-sm">Genera facturas, registra pagos, maneja seguro médico y envía recibos digitales. Todo automatizado.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-[#e8e0d6] p-8 hover:border-[#c75b4a]/20 hover:shadow-lg hover:shadow-[#c75b4a]/5 transition-all duration-300 reveal reveal-delay-2">
                        <div class="w-12 h-12 rounded-xl bg-[#c75b4a]/10 text-[#c75b4a] flex items-center justify-center mb-5">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-3">Recetas y Notas Clínicas</h3>
                        <p class="text-[#6b6358] leading-relaxed text-sm">Recetas digitales, notas SOAP y registros clínicos completos con historial de tratamientos.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-[#e8e0d6] p-8 hover:border-[#c75b4a]/20 hover:shadow-lg hover:shadow-[#c75b4a]/5 transition-all duration-300 reveal reveal-delay-3">
                        <div class="w-12 h-12 rounded-xl bg-[#c75b4a]/10 text-[#c75b4a] flex items-center justify-center mb-5">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-3">Equipo Multi-Rol</h3>
                        <p class="text-[#6b6358] leading-relaxed text-sm">Permisos granulares para doctores, asistentes y administradores. Tus datos siempre seguros.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== HOW IT WORKS ===== -->
        <section class="py-24 lg:py-32 bg-white border-y border-[#e8e0d6]">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="max-w-2xl mb-16 lg:mb-20 reveal">
                    <p class="text-sm font-semibold text-[#c75b4a] uppercase tracking-[0.2em] mb-5">Cómo funciona</p>
                    <h2 class="text-4xl lg:text-5xl font-serif text-[#1a1b2f] leading-[1.1] mb-6">Empieza en minutos</h2>
                    <p class="text-lg text-[#6b6358] leading-relaxed">Del registro a la primera consulta en menos de 10 minutos. Sin conocimientos técnicos.</p>
                </div>

                <div class="grid md:grid-cols-4 gap-10 lg:gap-14 relative">
                    <div class="hidden lg:block absolute top-12 left-[12%] right-[12%] h-px bg-[#e8e0d6]"></div>

                    <div class="relative text-center reveal reveal-delay-1">
                        <div class="w-20 h-20 rounded-2xl bg-[#1a1b2f] text-white flex items-center justify-center mx-auto mb-6 text-3xl font-serif shadow-xl relative z-10">1</div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-3">Regístrate</h3>
                        <p class="text-[#8a8072] leading-relaxed text-sm">Crea tu cuenta. 14 días de prueba sin tarjeta de crédito.</p>
                    </div>

                    <div class="relative text-center reveal reveal-delay-2">
                        <div class="w-20 h-20 rounded-2xl bg-[#c75b4a] text-white flex items-center justify-center mx-auto mb-6 text-3xl font-serif shadow-xl relative z-10">2</div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-3">Configura tu clínica</h3>
                        <p class="text-[#8a8072] leading-relaxed text-sm">Procedimientos, inventario, precios e invitación a tu equipo.</p>
                    </div>

                    <div class="relative text-center reveal reveal-delay-3">
                        <div class="w-20 h-20 rounded-2xl bg-[#1a1b2f] text-white flex items-center justify-center mx-auto mb-6 text-3xl font-serif shadow-xl relative z-10">3</div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-3">Registra pacientes</h3>
                        <p class="text-[#8a8072] leading-relaxed text-sm">Añade pacientes con historias clínicas completas.</p>
                    </div>

                    <div class="relative text-center reveal reveal-delay-4">
                        <div class="w-20 h-20 rounded-2xl bg-[#c75b4a] text-white flex items-center justify-center mx-auto mb-6 text-3xl font-serif shadow-xl relative z-10">4</div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-3">Comienza a tratar</h3>
                        <p class="text-[#8a8072] leading-relaxed text-sm">Agenda citas, crea planes y gestiona tu práctica.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== PRICING ===== -->
        <section id="pricing" class="py-24 lg:py-32">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="max-w-2xl mb-16 lg:mb-20 reveal">
                    <p class="text-sm font-semibold text-[#c75b4a] uppercase tracking-[0.2em] mb-5">Planes</p>
                    <h2 class="text-4xl lg:text-5xl font-serif text-[#1a1b2f] leading-[1.1] mb-6">Precios claros, sin sorpresas</h2>
                    <p class="text-lg text-[#6b6358] leading-relaxed">Empieza con 14 días gratis. Cancela cuando quieras.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <div class="relative bg-white rounded-2xl border border-[#e8e0d6] p-8 hover:border-[#1a1b2f]/20 transition-all duration-300 reveal reveal-delay-1">
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-2">Starter</h3>
                        <p class="text-sm text-[#8a8072] mb-6">Para consultorios pequeños</p>
                        <div class="mb-6">
                            <span class="text-5xl font-serif text-[#1a1b2f]">$39</span>
                            <span class="text-[#8a8072]">/mes</span>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Hasta 5 miembros
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Hasta 500 pacientes
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Odontograma básico
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Soporte por email
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block w-full text-center px-6 py-3 rounded-lg text-sm font-semibold text-[#1a1b2f] bg-[#f8f6f2] border border-[#e8e0d6] hover:bg-[#f0ece6] transition-all">Probar gratis</a>
                    </div>

                    <div class="relative bg-white rounded-2xl border-2 border-[#c75b4a] p-8 shadow-xl shadow-[#c75b4a]/10 scale-[1.02] reveal reveal-delay-2">
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-lg bg-[#c75b4a] text-white text-xs font-bold">Más popular</div>
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-2">Pro</h3>
                        <p class="text-sm text-[#8a8072] mb-6">Para clínicas en crecimiento</p>
                        <div class="mb-6">
                            <span class="text-5xl font-serif text-[#1a1b2f]">$89</span>
                            <span class="text-[#8a8072]">/mes</span>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Hasta 15 miembros
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Pacientes ilimitados
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Odontograma + presupuestos
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Portal del paciente
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Exportación a PDF
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Soporte prioritario
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block w-full text-center px-6 py-3 rounded-lg text-sm font-semibold text-white bg-[#c75b4a] hover:bg-[#b04a3a] shadow-lg shadow-[#c75b4a]/20 transition-all">Probar gratis</a>
                    </div>

                    <div class="relative bg-white rounded-2xl border border-[#e8e0d6] p-8 hover:border-[#1a1b2f]/20 transition-all duration-300 reveal reveal-delay-3">
                        <h3 class="text-xl font-bold text-[#1a1b2f] mb-2">Enterprise</h3>
                        <p class="text-sm text-[#8a8072] mb-6">Para grupos multi-sede</p>
                        <div class="mb-6">
                            <span class="text-5xl font-serif text-[#1a1b2f]">A medida</span>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Miembros ilimitados
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Multi-sede
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Integraciones personalizadas
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Gerente de cuenta dedicado
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#6b6358]">
                                <svg class="w-5 h-5 text-[#c75b4a] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                SLA garantizado
                            </li>
                        </ul>
                        <a href="#" class="block w-full text-center px-6 py-3 rounded-lg text-sm font-semibold text-[#1a1b2f] bg-[#f8f6f2] border border-[#e8e0d6] hover:bg-[#f0ece6] transition-all">Contactar</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== TESTIMONIALS ===== -->
        <section id="testimonials" class="py-24 lg:py-32 bg-white border-y border-[#e8e0d6] overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="max-w-2xl mb-16 lg:mb-20 reveal">
                    <p class="text-sm font-semibold text-[#c75b4a] uppercase tracking-[0.2em] mb-5">Testimonios</p>
                    <h2 class="text-4xl lg:text-5xl font-serif text-[#1a1b2f] leading-[1.1]">Lo que dicen nuestros colegas</h2>
                </div>

                <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">
                    <div class="break-inside-avoid bg-[#f8f6f2] rounded-2xl p-8 border border-[#e8e0d6] reveal">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-[#1a1b2f] flex items-center justify-center text-white font-bold font-serif text-lg">DR</div>
                            <div>
                                <p class="font-bold text-[#1a1b2f]">Dr. Ricardo Méndez</p>
                                <p class="text-sm text-[#8a8072]">DentalCare Plus, Caracas</p>
                            </div>
                        </div>
                        <p class="text-[#6b6358] leading-relaxed">"DentalFlow transformó nuestra práctica. El odontograma nos ahorra horas cada semana. Nuestros pacientes aman el portal."</p>
                    </div>

                    <div class="break-inside-avoid bg-[#f8f6f2] rounded-2xl p-8 border border-[#e8e0d6] reveal reveal-delay-1">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-[#c75b4a] flex items-center justify-center text-white font-bold font-serif text-lg">SL</div>
                            <div>
                                <p class="font-bold text-[#1a1b2f]">Dra. Sara López</p>
                                <p class="text-sm text-[#8a8072]">Sonrisa Perfecta, Valencia</p>
                            </div>
                        </div>
                        <p class="text-[#6b6358] leading-relaxed">"Generar presupuestos desde el odontograma es brillante. Lo que tomaba 30 minutos ahora toma segundos."</p>
                    </div>

                    <div class="break-inside-avoid bg-[#f8f6f2] rounded-2xl p-8 border border-[#e8e0d6] reveal reveal-delay-2">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-[#1a1b2f] flex items-center justify-center text-white font-bold font-serif text-lg">CG</div>
                            <div>
                                <p class="font-bold text-[#1a1b2f]">Dr. Carlos Gómez</p>
                                <p class="text-sm text-[#8a8072]">Clínica Dental CG, Maracaibo</p>
                            </div>
                        </div>
                        <p class="text-[#6b6358] leading-relaxed">"El acceso multi-rol con permisos es perfecto. Asistentes, doctores y administradores siempre sincronizados."</p>
                    </div>

                    <div class="break-inside-avoid bg-[#f8f6f2] rounded-2xl p-8 border border-[#e8e0d6] reveal reveal-delay-3">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-[#c75b4a] flex items-center justify-center text-white font-bold font-serif text-lg">AM</div>
                            <div>
                                <p class="font-bold text-[#1a1b2f]]">Dra. Andrea Mendoza</p>
                                <p class="text-sm text-[#8a8072]">ODONTOLIFE, Barquisimeto</p>
                            </div>
                        </div>
                        <p class="text-[#6b6358] leading-relaxed">"Migramos de papeles a DentalFlow. La migración fue muy suave y el equipo de soporte es excepcional."</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== FAQ ===== -->
        <section id="faq" class="py-24 lg:py-32">
            <div class="max-w-3xl mx-auto px-6 lg:px-12">
                <div class="max-w-2xl mb-16 reveal">
                    <p class="text-sm font-semibold text-[#c75b4a] uppercase tracking-[0.2em] mb-5">FAQ</p>
                    <h2 class="text-4xl lg:text-5xl font-serif text-[#1a1b2f] leading-[1.1]">Preguntas frecuentes</h2>
                </div>

                <div class="space-y-3" x-data="{ active: null }">
                    <div class="bg-white rounded-xl border border-[#e8e0d6] overflow-hidden transition-all duration-200" x-bind:class="active === 1 ? 'border-[#c75b4a]/30' : ''">
                        <button x-on:click="active = active === 1 ? null : 1" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#1a1b2f] pr-4">¿Hay prueba gratuita?</span>
                            <svg class="w-5 h-5 text-[#8a8072] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="active === 1" x-collapse>
                            <div class="px-6 pb-5 text-[#6b6358] leading-relaxed">¡Sí! Ofrecemos 14 días de prueba gratuita con acceso completo a todas las funciones Pro. Sin tarjeta de crédito. Puedes cancelar cuando quieras.</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-[#e8e0d6] overflow-hidden transition-all duration-200" x-bind:class="active === 2 ? 'border-[#c75b4a]/30' : ''">
                        <button x-on:click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#1a1b2f] pr-4">¿Puedo cambiar de plan después?</span>
                            <svg class="w-5 h-5 text-[#8a8072] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="active === 2" x-collapse>
                            <div class="px-6 pb-5 text-[#6b6358] leading-relaxed">Absolutamente. Puedes subir o bajar de plan cuando quieras. Las mejoras son inmediatas y las downgrades aplican al siguiente ciclo.</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-[#e8e0d6] overflow-hidden transition-all duration-200" x-bind:class="active === 3 ? 'border-[#c75b4a]/30' : ''">
                        <button x-on:click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#1a1b2f] pr-4">¿Mis datos están seguros?</span>
                            <svg class="w-5 h-5 text-[#8a8072] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="active === 3" x-collapse>
                            <div class="px-6 pb-5 text-[#6b6358] leading-relaxed">Sí. Usamos cifrado de nivel industrial. Nuestra infraestructura corre en servidores seguros con respaldos diarios.</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-[#e8e0d6] overflow-hidden transition-all duration-200" x-bind:class="active === 4 ? 'border-[#c75b4a]/30' : ''">
                        <button x-on:click="active = active === 4 ? null : 4" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#1a1b2f] pr-4">¿Puedo migrar mis datos desde otro sistema?</span>
                            <svg class="w-5 h-5 text-[#8a8072] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="active === 4" x-collapse>
                            <div class="px-6 pb-5 text-[#6b6358] leading-relaxed">Sí. Te ayudamos a migrar tus historias clínicas, citas y datos de pacientes. Contáctanos y te guiamos en el proceso.</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-[#e8e0d6] overflow-hidden transition-all duration-200" x-bind:class="active === 5 ? 'border-[#c75b4a]/30' : ''">
                        <button x-on:click="active = active === 5 ? null : 5" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#1a1b2f] pr-4">¿Ofrecen descuento por pago anual?</span>
                            <svg class="w-5 h-5 text-[#8a8072] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="active === 5" x-collapse>
                            <div class="px-6 pb-5 text-[#6b6358] leading-relaxed">¡Sí! Ahorra 20% con facturación anual. El descuento se aplica automáticamente. Consulta por precios corporativos.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== CTA ===== -->
        <section class="py-20 lg:py-28 reveal">
            <div class="max-w-5xl mx-auto px-6 lg:px-12">
                <div class="relative bg-[#1a1b2f] rounded-[2rem] p-14 lg:p-20 text-center text-white overflow-hidden">
                    <div class="absolute top-0 right-0 w-72 h-72 bg-[#c75b4a]/10 rounded-full -translate-y-1/3 translate-x-1/4"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-[#c75b4a]/10 rounded-full translate-y-1/3 -translate-x-1/4"></div>

                    <div class="relative z-10">
                        <h2 class="text-3xl lg:text-5xl font-serif leading-[1.1] mb-6">¿Listo para modernizar tu clínica?</h2>
                        <p class="text-white/60 text-lg mb-10 max-w-xl mx-auto leading-relaxed">Únete a miles de odontólogos que confían en DentalFlow. Empieza tu prueba gratuita de 14 días.</p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-lg text-base font-semibold text-[#1a1b2f] bg-white hover:bg-[#f0ece6] shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                                Empezar prueba gratis
                                <svg class="ml-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="#features" class="inline-flex items-center justify-center px-8 py-4 rounded-lg text-base font-semibold text-white bg-white/10 border border-white/20 hover:bg-white/20 backdrop-blur-sm transition-all duration-200">
                                <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Ver demo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== FOOTER ===== -->
        <footer class="border-t border-[#e8e0d6] py-16">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="grid md:grid-cols-4 gap-8 mb-12">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="relative flex items-center justify-center w-9 h-9">
                                <span class="absolute inset-0 bg-[#c75b4a] rounded-xl rotate-6"></span>
                                <span class="absolute inset-0 bg-[#1a1b2f] rounded-xl -rotate-3"></span>
                                <span class="relative text-white font-bold text-xs tracking-tight z-10">DF</span>
                            </span>
                            <span class="text-lg font-bold text-[#1a1b2f]">DentalFlow</span>
                        </div>
                        <p class="text-sm text-[#8a8072] max-w-sm leading-relaxed">Plataforma moderna de gestión dental. Potenciando clínicas con herramientas digitales para una mejor atención al paciente.</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-[#1a1b2f] mb-4">Producto</h4>
                        <ul class="space-y-3">
                            <li><a href="#features" class="text-sm text-[#8a8072] hover:text-[#c75b4a] transition-colors">Funciones</a></li>
                            <li><a href="#pricing" class="text-sm text-[#8a8072] hover:text-[#c75b4a] transition-colors">Planes</a></li>
                            <li><a href="#faq" class="text-sm text-[#8a8072] hover:text-[#c75b4a] transition-colors">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-[#1a1b2f] mb-4">Compañía</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-sm text-[#8a8072] hover:text-[#c75b4a] transition-colors">Blog</a></li>
                            <li><a href="#" class="text-sm text-[#8a8072] hover:text-[#c75b4a] transition-colors">Contacto</a></li>
                            <li><a href="#" class="text-sm text-[#8a8072] hover:text-[#c75b4a] transition-colors">Términos</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-[#e8e0d6] pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-[#8a8072]">&copy; {{ date('Y') }} DentalFlow. Todos los derechos reservados.</p>
                    <div class="flex gap-6">
                        <a href="#" class="text-sm text-[#8a8072] hover:text-[#c75b4a] transition-colors">Privacidad</a>
                        <a href="#" class="text-sm text-[#8a8072] hover:text-[#c75b4a] transition-colors">Términos</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reveals = document.querySelectorAll('.reveal');
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                        }
                    });
                },
                { threshold: 0.1, rootMargin: '0px 0px -60px 0px' }
            );
            reveals.forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>
