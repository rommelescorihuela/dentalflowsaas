<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'DentalFlow') }} — Gestión Dental Profesional</title>
    <meta name="description" content="Plataforma SaaS para gestión de clínicas dentales en Venezuela. Odontograma interactivo, historias clínicas, facturación y más.">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_VE">
    <meta property="og:site_name" content="{{ config('app.name', 'DentalFlow') }}">
    <meta property="og:title" content="DentalFlow — Gestión dental sin fricción">
    <meta property="og:description" content="Odontograma digital, historias clínicas y facturación en un solo lugar, pensado para odontólogos venezolanos.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ url('/images/landing/og.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="DentalFlow — Gestión dental sin fricción">
    <meta name="twitter:description" content="Odontograma digital, historias clínicas y facturación para clínicas en Venezuela.">
    <meta name="twitter:image" content="{{ url('/images/landing/og.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .reveal { opacity: 0; transform: translateY(24px); transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.12s; }
        .reveal-delay-2 { transition-delay: 0.24s; }
        .reveal-delay-3 { transition-delay: 0.36s; }
        .reveal-delay-4 { transition-delay: 0.48s; }

        /* ===== Fondos con imagen (self-hosted; reemplazar por fotos reales de la clínica) ===== */
        /* ===== Fondos fotográficos (CC0 / dominio público) traslúcidos ===== */
        .photo-hero { background-image: url('/images/landing/photo-office.webp'); }
        .photo-cta { background-image: url('/images/landing/photo-office2.webp'); }
        .photo-soft { background-image: url('/images/landing/photo-reception.webp'); }

        /* ===== Motivos dentales traslúcidos (imágenes de fondo de marca) ===== */
        .bg-motif-tooth { background-image: url('/images/landing/motif-tooth.svg'); background-repeat: no-repeat; background-size: contain; }
        .bg-motif-arch { background-image: url('/images/landing/motif-arch.svg'); background-repeat: no-repeat; background-size: contain; }
        .bg-motif-badge { background-image: url('/images/landing/motif-tooth-badge.svg'); background-repeat: no-repeat; background-size: contain; }

        /* ===== Spotlight que sigue el cursor ===== */
        .hero-spotlight {
            background: radial-gradient(560px circle at var(--mx, 50%) var(--my, 38%), rgba(31, 158, 143, 0.16), transparent 62%);
        }
        .noise-overlay {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3'/%3E%3C/filter%3E%3Crect width='120' height='120' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        /* ===== Movimiento ===== */
        @keyframes dfFloaty { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .reveal { opacity: 1; transform: none; transition: none; }
            .animate-blob { animation: none; }
            .hero-spotlight { display: none; }
        }
    </style>
    <noscript><style>.reveal { opacity: 1 !important; transform: none !important; }</style></noscript>
</head>

<body class="antialiased bg-[#faf9f6] text-[#211f1b] selection:bg-[#1f9e8f] selection:text-white font-sans">
@php
    $contactWhatsapp = config('contact.whatsapp');
    $contactEmail = config('contact.email');
    $contactHref = $contactWhatsapp ? 'https://wa.me/'.$contactWhatsapp : 'mailto:'.$contactEmail;
    $contactExternal = $contactWhatsapp ? ' target="_blank" rel="noopener"' : '';
    $waitlist = config('contact.waitlist');
@endphp
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:bg-[#157e73] focus:text-white focus:px-4 focus:py-2 focus:rounded-lg">Saltar al contenido</a>

    <div x-data="{ mobileOpen: false, scrolled: false }" x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)" class="relative">

        <!-- ===== NAVBAR ===== -->
        <nav x-bind:class="scrolled ? 'bg-[#faf9f6]/95 backdrop-blur-lg shadow-lg border-b border-[#e8e4da]' : 'bg-transparent'" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="flex items-center justify-between h-20">
                    <a href="/" class="flex items-center gap-3 group">
                        <span class="relative flex items-center justify-center w-10 h-10">
                            <span class="absolute inset-0 bg-[#1f9e8f] rounded-xl rotate-6 group-hover:rotate-12 transition-transform duration-300"></span>
                            <span class="absolute inset-0 bg-[#211f1b] rounded-xl -rotate-3 group-hover:rotate-0 transition-transform duration-300"></span>
                            <span class="relative text-white font-bold text-sm tracking-tight z-10">DF</span>
                        </span>
                        <span class="hidden sm:inline text-lg font-bold text-[#211f1b] tracking-tight">DentalFlow</span>
                    </a>

                    <div class="hidden md:flex items-center gap-1">
                        <a href="#features" class="px-4 py-2 text-sm font-medium text-[#625c51] hover:text-[#157e73] rounded-lg hover:bg-[#1f9e8f]/5 transition-all duration-200">Funciones</a>
                        <a href="#pricing" class="px-4 py-2 text-sm font-medium text-[#625c51] hover:text-[#157e73] rounded-lg hover:bg-[#1f9e8f]/5 transition-all duration-200">Planes</a>
                        <a href="#testimonials" class="px-4 py-2 text-sm font-medium text-[#625c51] hover:text-[#157e73] rounded-lg hover:bg-[#1f9e8f]/5 transition-all duration-200">Fundadoras</a>
                        <a href="#faq" class="px-4 py-2 text-sm font-medium text-[#625c51] hover:text-[#157e73] rounded-lg hover:bg-[#1f9e8f]/5 transition-all duration-200">FAQ</a>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="hidden sm:inline-flex items-center px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-[#157e73] hover:bg-[#14645c] shadow-lg shadow-[#157e73]/20 transition-all duration-200">
                                    Panel
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center whitespace-nowrap px-2 sm:px-4 py-2 text-sm font-medium text-[#625c51] hover:text-[#157e73] transition-colors">Iniciar sesión</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" x-show="!mobileOpen" class="inline-flex items-center whitespace-nowrap px-4 sm:px-5 py-3 rounded-lg text-sm font-semibold text-white bg-[#157e73] hover:bg-[#14645c] shadow-lg shadow-[#157e73]/20 transition-all duration-200">
                                        Empezar gratis
                                    </a>
                                @endif
                            @endauth
                        @endif
                        <button x-on:click="mobileOpen = !mobileOpen" x-bind:aria-expanded="mobileOpen" aria-controls="mobile-nav" class="md:hidden p-2.5 rounded-lg text-[#625c51] hover:text-[#157e73] hover:bg-[#1f9e8f]/5 transition-all" aria-label="Menú">
                            <svg x-show="!mobileOpen" class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                            <svg x-show="mobileOpen" class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div x-show="mobileOpen" x-cloak id="mobile-nav" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-3" class="md:hidden border-t border-[#e8e4da] bg-[#faf9f6] shadow-xl">
                <div class="px-6 py-5 space-y-1">
                    <a href="#features" x-on:click="mobileOpen = false" class="block px-3 py-3 rounded-lg text-[#625c51] font-medium hover:text-[#157e73] hover:bg-[#1f9e8f]/5 transition-all">Funciones</a>
                    <a href="#pricing" x-on:click="mobileOpen = false" class="block px-3 py-3 rounded-lg text-[#625c51] font-medium hover:text-[#157e73] hover:bg-[#1f9e8f]/5 transition-all">Planes</a>
                    <a href="#testimonials" x-on:click="mobileOpen = false" class="block px-3 py-3 rounded-lg text-[#625c51] font-medium hover:text-[#157e73] hover:bg-[#1f9e8f]/5 transition-all">Fundadoras</a>
                    <a href="#faq" x-on:click="mobileOpen = false" class="block px-3 py-3 rounded-lg text-[#625c51] font-medium hover:text-[#157e73] hover:bg-[#1f9e8f]/5 transition-all">FAQ</a>
                    <div class="border-t border-[#e8e4da] pt-4 mt-3 space-y-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" x-on:click="mobileOpen = false" class="block w-full text-center px-5 py-3 rounded-lg text-sm font-semibold text-white bg-[#157e73]">Panel</a>
                            @else
                                <a href="{{ route('login') }}" x-on:click="mobileOpen = false" class="block w-full text-center px-5 py-3 rounded-lg text-sm font-semibold text-[#211f1b] bg-white border border-[#e8e4da]">Iniciar sesión</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" x-on:click="mobileOpen = false" class="block w-full text-center px-5 py-3 rounded-lg text-sm font-semibold text-white bg-[#157e73]">Empezar gratis</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <main id="main-content">
        <!-- ===== HERO ===== -->
        <section
            x-data="{
                mx: 50, my: 38,
                track(e) {
                    const r = e.currentTarget.getBoundingClientRect();
                    this.mx = ((e.clientX - r.left) / r.width) * 100;
                    this.my = ((e.clientY - r.top) / r.height) * 100;
                },
                reset() { this.mx = 50; this.my = 38; }
            }"
            x-on:mousemove="track($event)"
            x-on:mouseleave="reset()"
            class="relative min-h-0 lg:min-h-[92vh] flex items-center pt-20 lg:pt-24 overflow-hidden">
            <!-- Fondos: foto tenue + grilla técnica + spotlight que sigue el cursor + orbes + ruido -->
            <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
                <div class="photo-hero absolute inset-0 bg-cover bg-center opacity-[0.16]" style="filter: saturate(0.7) contrast(1.02);"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-[#faf9f6]/70 via-[#faf9f6]/88 to-[#faf9f6]"></div>
                <div class="hero-spotlight absolute inset-0" :style="'--mx:' + mx + '%;--my:' + my + '%'"></div>
                <div class="absolute top-[10%] -left-32 w-[560px] h-[560px] rounded-full blur-3xl opacity-40 animate-blob" style="background: radial-gradient(circle, rgba(31,158,143,.30), transparent 62%);"></div>
                <div class="absolute bottom-[-12%] right-[-8%] w-[460px] h-[460px] rounded-full blur-3xl opacity-30 animate-blob animation-delay-2000" style="background: radial-gradient(circle, rgba(195,103,47,.26), transparent 62%);"></div>
                <div class="bg-motif-tooth absolute -left-24 top-[16%] w-[280px] h-[380px] opacity-[0.06]"></div>
                <svg class="absolute left-1/2 bottom-[-40px] -translate-x-1/2 w-[1200px] max-w-none text-[#1f9e8f] opacity-[0.07]" viewBox="0 0 1200 340" fill="none" aria-hidden="true">
                    <path d="M140 70 Q600 330 1060 70" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    <path d="M220 92 Q600 300 980 92" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    <path d="M300 114 Q600 268 900 114" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <div class="noise-overlay absolute inset-0 opacity-[0.04]"></div>
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-12 w-full relative z-10">
                <div class="grid lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                    <div class="lg:col-span-5 pt-8 lg:pt-0">
                        <p class="text-xs font-semibold text-[#157e73] uppercase tracking-[0.22em] mb-5 reveal visible">Plataforma todo-en-uno</p>

                        <h1 class="font-display text-[2.7rem] leading-[1.02] sm:text-6xl lg:text-7xl font-bold tracking-[-0.03em] text-[#211f1b] mb-6 reveal visible">
                            Gestiona tu clínica dental
                            <span class="block text-[#1f9e8f]">sin fricción.</span>
                        </h1>

                        <p class="text-lg text-[#625c51] leading-relaxed max-w-lg mb-9 reveal visible reveal-delay-1">
                            Odontograma digital, historias clínicas y facturación en un solo lugar, pensado para odontólogos venezolanos.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 reveal visible reveal-delay-2">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-lg text-base font-semibold text-white bg-[#157e73] hover:bg-[#14645c] shadow-xl shadow-[#157e73]/20 hover:-translate-y-0.5 transition-all duration-200">
                                Empezar gratis
                                <svg class="ml-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="#features" class="inline-flex items-center justify-center px-8 py-4 rounded-lg text-base font-semibold text-[#211f1b] bg-white border border-[#e8e4da] hover:border-[#1f9e8f]/30 hover:bg-[#1f9e8f]/5 shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5h6v6H4V5Zm10 0h6v6h-6V5ZM4 13h6v6H4v-6Zm10 0h6v6h-6v-6Z"/></svg>
                                Ver funciones
                            </a>
                        </div>

                    </div>

                    <div class="lg:col-span-7 min-w-0 relative reveal visible reveal-delay-2"
                         x-data="{
                            fullOrder: ['healthy','caries','filled','endodontic','crown','missing'],
                            shortOrder: ['healthy','caries','filled'],
                            advanced: false,
                            labels: { healthy:'Sano', caries:'Caries', filled:'Obturación', endodontic:'Endodoncia', crown:'Corona', missing:'Ausente' },
                            colors: { healthy:'#ffffff', caries:'#b02f4a', filled:'#157e73', endodontic:'#8a5a00', crown:'#7c3aed', missing:'#211f1b' },
                            legend: ['caries','filled','endodontic','crown','missing'],
                            upperRight:[18,17,16,15,14,13,12,11], upperLeft:[21,22,23,24,25,26,27,28],
                            lowerRight:[48,47,46,45,44,43,42,41], lowerLeft:[31,32,33,34,35,36,37,38],
                            teeth:{},
                            seed: { 16:'caries', 26:'filled', 36:'endodontic', 46:'crown', 18:'missing', 47:'caries' },
                            init(){ this.reset(); },
                            reset(){ ['upperRight','upperLeft','lowerRight','lowerLeft'].forEach(k => this[k].forEach(n => this.teeth[n] = this.seed[n] || 'healthy')); },
                            order(){ return this.advanced ? this.fullOrder : this.shortOrder; },
                            visibleLegend(){ return this.advanced ? ['healthy','caries','filled','endodontic','crown','missing'] : ['healthy','caries','filled']; },
                            cycle(n){ const o = this.order(); const i = o.indexOf(this.teeth[n]); this.teeth[n] = (i === -1) ? o[0] : o[(i + 1) % o.length]; },
                            color(s){ return this.colors[s]; },
                            label(s){ return this.labels[s]; },
                            short(s){ return { caries:'C', filled:'O', endodontic:'E', crown:'Co', missing:'X' }[s] || ''; },
                            count(){ return Object.values(this.teeth).filter(s => s !== 'healthy').length; },
                            countLabel(){ const c = this.count(); if (c === 0) return 'Sin hallazgos en el plan'; if (c === 1) return '1 hallazgo en el plan'; if (c > 12) return 'Muchos hallazgos en el plan'; return c + ' hallazgos en el plan'; }
                         }">
                        <div class="relative min-w-0 bg-white rounded-2xl border border-[#e8e4da] shadow-2xl shadow-[#1f9e8f]/10 overflow-hidden">
                            <div class="px-5 py-3 border-b border-[#e8e4da] flex items-center justify-between bg-[#f4f1ea]">
                                <span class="text-xs font-semibold text-[#6b6459]">Odontograma · DentalFlow</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#14645c] bg-[#1f9e8f]/15 px-2 py-0.5 rounded">Demo</span>
                            </div>
                            <div class="p-5 sm:p-6">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-xs font-semibold text-[#157e73] uppercase tracking-wider">Demo interactivo</p>
                                        <p class="text-sm text-[#6b6459] mt-0.5">Toca un diente para cambiar su diagnóstico</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" x-on:click="advanced = !advanced" :aria-pressed="advanced" class="inline-flex items-center min-h-[44px] px-3 text-xs font-medium text-[#157e73] hover:underline" x-text="advanced ? 'Menos estados' : 'Más estados'"></button>
                                        <button type="button" x-on:click="reset()" class="inline-flex items-center min-h-[44px] px-3 text-xs font-medium text-[#157e73] hover:underline">Reiniciar</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <p class="text-[10px] uppercase tracking-[0.15em] text-[#6b6459] mb-1.5">Estados del diente</p>
                                    <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-[#6b6459]">
                                        <template x-for="s in visibleLegend()" :key="s">
                                            <span class="inline-flex items-center gap-1.5">
                                                <span class="w-3.5 h-3.5 rounded-sm border border-black/15" :style="'background-color:' + colors[s]"></span>
                                                <span x-text="labels[s]"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div class="relative">
                                <div class="overflow-x-auto -mx-1 px-1">
                                <div class="min-w-[820px] lg:min-w-0 space-y-2">
                                    <div class="flex items-stretch gap-2">
                                        <div class="grid grid-cols-8 gap-1.5 flex-1">
                                            <template x-for="n in upperRight" :key="n">
                                                <button type="button" x-on:click="cycle(n)" :aria-label="'Diente ' + n + ': ' + label(teeth[n])" :title="'Diente ' + n + ': ' + label(teeth[n])"
                                                        class="group flex flex-col items-center gap-1 focus:outline-none">
                                                    <span class="text-[11px] font-bold text-[#6b6459]" x-text="n"></span>
                                                    <span class="w-full aspect-square rounded-md border border-[#d6d0c2] flex items-center justify-center transition-transform motion-safe:group-active:scale-90 group-focus-visible:ring-2 group-focus-visible:ring-[#157e73]"
                                                          :style="'background-color:' + color(teeth[n])">
                                                        <span x-show="teeth[n] !== 'healthy'" class="text-[11px] font-bold text-white" x-text="short(teeth[n])"></span>
                                                    </span>
                                                </button>
                                            </template>
                                        </div>
                                        <div class="w-px bg-[#e8e4da]"></div>
                                        <div class="grid grid-cols-8 gap-1.5 flex-1">
                                            <template x-for="n in upperLeft" :key="n">
                                                <button type="button" x-on:click="cycle(n)" :aria-label="'Diente ' + n + ': ' + label(teeth[n])" :title="'Diente ' + n + ': ' + label(teeth[n])"
                                                        class="group flex flex-col items-center gap-1 focus:outline-none">
                                                    <span class="text-[11px] font-bold text-[#6b6459]" x-text="n"></span>
                                                    <span class="w-full aspect-square rounded-md border border-[#d6d0c2] flex items-center justify-center transition-transform motion-safe:group-active:scale-90 group-focus-visible:ring-2 group-focus-visible:ring-[#157e73]"
                                                          :style="'background-color:' + color(teeth[n])">
                                                        <span x-show="teeth[n] !== 'healthy'" class="text-[11px] font-bold text-white" x-text="short(teeth[n])"></span>
                                                    </span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="flex items-stretch gap-2">
                                        <div class="grid grid-cols-8 gap-1.5 flex-1">
                                            <template x-for="n in lowerRight" :key="n">
                                                <button type="button" x-on:click="cycle(n)" :aria-label="'Diente ' + n + ': ' + label(teeth[n])" :title="'Diente ' + n + ': ' + label(teeth[n])"
                                                        class="group flex flex-col items-center gap-1 focus:outline-none">
                                                    <span class="text-[11px] font-bold text-[#6b6459]" x-text="n"></span>
                                                    <span class="w-full aspect-square rounded-md border border-[#d6d0c2] flex items-center justify-center transition-transform motion-safe:group-active:scale-90 group-focus-visible:ring-2 group-focus-visible:ring-[#157e73]"
                                                          :style="'background-color:' + color(teeth[n])">
                                                        <span x-show="teeth[n] !== 'healthy'" class="text-[11px] font-bold text-white" x-text="short(teeth[n])"></span>
                                                    </span>
                                                </button>
                                            </template>
                                        </div>
                                        <div class="w-px bg-[#e8e4da]"></div>
                                        <div class="grid grid-cols-8 gap-1.5 flex-1">
                                            <template x-for="n in lowerLeft" :key="n">
                                                <button type="button" x-on:click="cycle(n)" :aria-label="'Diente ' + n + ': ' + label(teeth[n])" :title="'Diente ' + n + ': ' + label(teeth[n])"
                                                        class="group flex flex-col items-center gap-1 focus:outline-none">
                                                    <span class="text-[11px] font-bold text-[#6b6459]" x-text="n"></span>
                                                    <span class="w-full aspect-square rounded-md border border-[#d6d0c2] flex items-center justify-center transition-transform motion-safe:group-active:scale-90 group-focus-visible:ring-2 group-focus-visible:ring-[#157e73]"
                                                          :style="'background-color:' + color(teeth[n])">
                                                        <span x-show="teeth[n] !== 'healthy'" class="text-[11px] font-bold text-white" x-text="short(teeth[n])"></span>
                                                    </span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <div class="pointer-events-none absolute inset-y-0 right-0 w-10 bg-gradient-to-l from-white to-transparent lg:hidden" aria-hidden="true"></div>
                                </div>
                                <p class="lg:hidden text-[11px] text-[#6b6459] mt-1 text-center">Desliza para ver toda la arcada →</p>

                                <div class="mt-5 pt-4 border-t border-[#e8e4da] flex items-center justify-end">
                                    <span class="text-xs font-semibold text-[#211f1b]" aria-live="polite" x-text="countLabel()">Sin hallazgos en el plan</span>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-28 h-28 bg-[#1f9e8f]/15 rounded-3xl -z-10 blur-2xl"></div>
                        <div class="absolute -top-6 -left-6 w-24 h-24 bg-[#c3672f]/10 rounded-3xl -z-10 blur-2xl"></div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 hidden lg:flex flex-col items-center gap-2 text-[#6b6459]" aria-hidden="true">
                <span class="text-[10px] uppercase tracking-[0.25em]">Desliza</span>
                <span class="w-px h-10 bg-gradient-to-b from-[#1f9e8f] to-transparent"></span>
            </div>
        </section>

        <!-- ===== TRUST BAR (credibilidad) ===== -->
        <section class="relative py-16 bg-white border-y border-[#e8e4da] overflow-hidden">
            <div class="relative max-w-5xl mx-auto px-6 lg:px-12">
                <p class="text-sm text-[#6b6459] text-center mb-8">Diseñado junto a clínicas en Venezuela. <span class="text-[#211f1b] font-semibold">En beta privada.</span></p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-[#e8e4da] bg-[#faf9f6] px-5 py-4">
                        <p class="font-semibold text-[#211f1b]">Tus datos, tuyos</p>
                        <p class="text-xs text-[#6b6459] mt-1 leading-relaxed">Conexión cifrada, acceso por roles y exportación completa en PDF o CSV.</p>
                    </div>
                    <div class="rounded-xl border border-[#e8e4da] bg-[#faf9f6] px-5 py-4">
                        <p class="font-semibold text-[#211f1b]">Hecho para Venezuela</p>
                        <p class="text-xs text-[#6b6459] mt-1 leading-relaxed">Odontograma, presupuestos y facturación pensados para tu día a día.</p>
                    </div>
                    <div class="rounded-xl border border-[#e8e4da] bg-[#faf9f6] px-5 py-4">
                        <p class="font-semibold text-[#211f1b]">Soporte en español</p>
                        <p class="text-xs text-[#6b6459] mt-1 leading-relaxed">Acompañamiento directo durante la beta, de persona a persona.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== FEATURES ===== -->
        <section id="features" class="relative scroll-mt-24 py-24 lg:py-32 overflow-hidden">
            <div class="photo-soft absolute inset-0 bg-cover bg-center opacity-[0.05] pointer-events-none" style="filter: saturate(0.7);" aria-hidden="true"></div>
            <div class="bg-motif-badge absolute right-[-70px] top-24 w-[280px] h-[280px] opacity-[0.05] pointer-events-none" aria-hidden="true"></div>
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="max-w-2xl mb-16 lg:mb-20 reveal">
                    <h2 class="text-4xl lg:text-5xl font-display font-bold tracking-tight text-[#211f1b] leading-[1.1] mb-6">Todo lo que necesitas para tu clínica</h2>
                    <p class="text-lg text-[#625c51] leading-relaxed">Una plataforma unificada, diseñada para el odontólogo venezolano. Sin complicaciones.</p>
                </div>

                <div class="space-y-6">
                    <!-- Showcase destacado -->
                    <div class="group relative overflow-hidden rounded-2xl border border-[#e8e4da] bg-white reveal reveal-delay-1">
                        <div class="grid md:grid-cols-2 items-center">
                            <div class="p-8 lg:p-10">
                                <span class="w-12 h-12 rounded-xl bg-[#157e73] text-white flex items-center justify-center mb-5 shadow-lg shadow-[#1f9e8f]/25">
                                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                </span>
                                <h3 class="text-2xl font-bold text-[#211f1b] mb-3">Historias clínicas completas</h3>
                                <p class="text-[#625c51] leading-relaxed max-w-md">Expedientes digitales con historial médico, alergias, odontograma y plan de tratamiento. Todo accesible desde cualquier lugar.</p>
                            </div>
                            <div class="px-6 pb-6 md:p-8 md:pl-0">
                                <img src="/images/landing/historia.png"
                                     alt="Registros clínicos de un paciente en DentalFlow: diente, superficie, estado y acción"
                                     class="w-full h-auto rounded-xl border border-[#e8e4da] bg-[#f4f1ea]" onerror="this.style.visibility='hidden'"
                                     width="1932" height="956" loading="lazy" decoding="async">
                                <p class="text-xs text-[#6b6459] mt-2">Captura del producto · ilustrativo</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="bg-white rounded-2xl border border-[#e8e4da] p-8 hover:border-[#1f9e8f]/20 hover:shadow-lg hover:shadow-[#1f9e8f]/5 transition-all duration-300 reveal reveal-delay-2">
                        <div class="w-12 h-12 rounded-xl bg-[#1f9e8f]/10 text-[#1f9e8f] flex items-center justify-center mb-5">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#211f1b] mb-3">Odontograma Interactivo</h3>
                        <p class="text-[#625c51] leading-relaxed text-sm">Plan de tratamiento visual con diagrama dental avanzado. Diagnostica y genera presupuestos al instante.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-[#e8e4da] p-8 hover:border-[#1f9e8f]/20 hover:shadow-lg hover:shadow-[#1f9e8f]/5 transition-all duration-300 reveal reveal-delay-3">
                        <div class="w-12 h-12 rounded-xl bg-[#1f9e8f]/10 text-[#1f9e8f] flex items-center justify-center mb-5">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0h18M5.25 12h13.5M5.25 15h13.5M5.25 18h13.5"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#211f1b] mb-3">Agenda Inteligente</h3>
                        <p class="text-[#625c51] leading-relaxed text-sm">Agendamiento con recordatorios automáticos, sincronización de calendario y disponibilidad en tiempo real.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-[#e8e4da] p-8 hover:border-[#1f9e8f]/20 hover:shadow-lg hover:shadow-[#1f9e8f]/5 transition-all duration-300 reveal reveal-delay-1">
                        <div class="w-12 h-12 rounded-xl bg-[#1f9e8f]/10 text-[#1f9e8f] flex items-center justify-center mb-5">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#211f1b] mb-3">Facturación y Pagos</h3>
                        <p class="text-[#625c51] leading-relaxed text-sm">Genera facturas, registra pagos, maneja seguro médico y envía recibos digitales. Todo automatizado.</p>
                    </div>

                </div>
                </div>
            </div>
        </section>

        <!-- ===== HOW IT WORKS ===== -->
        <section class="py-24 lg:py-32 bg-white border-y border-[#e8e4da]">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="max-w-2xl mb-16 lg:mb-20 reveal">
                    <h2 class="text-4xl lg:text-5xl font-display font-bold tracking-tight text-[#211f1b] leading-[1.1] mb-6">Empieza en minutos</h2>
                    <p class="text-lg text-[#625c51] leading-relaxed">Del registro a la primera consulta en menos de 10 minutos. Sin conocimientos técnicos.</p>
                </div>

                <div class="grid md:grid-cols-4 gap-10 lg:gap-14 relative">
                    <div class="hidden lg:block absolute top-12 left-[12%] right-[12%] h-px bg-[#e8e4da]"></div>

                    <div class="relative text-center reveal reveal-delay-1">
                        <div class="w-20 h-20 rounded-2xl bg-[#211f1b] text-white flex items-center justify-center mx-auto mb-6 text-3xl font-display shadow-xl relative z-10">1</div>
                        <h3 class="text-xl font-bold text-[#211f1b] mb-3">Regístrate</h3>
                        <p class="text-[#6b6459] leading-relaxed text-sm">Crea tu cuenta. 14 días de prueba sin tarjeta de crédito.</p>
                    </div>

                    <div class="relative text-center reveal reveal-delay-2">
                        <div class="w-20 h-20 rounded-2xl bg-[#157e73] text-white flex items-center justify-center mx-auto mb-6 text-3xl font-display shadow-xl relative z-10">2</div>
                        <h3 class="text-xl font-bold text-[#211f1b] mb-3">Configura tu clínica</h3>
                        <p class="text-[#6b6459] leading-relaxed text-sm">Procedimientos, inventario, precios e invitación a tu equipo.</p>
                    </div>

                    <div class="relative text-center reveal reveal-delay-3">
                        <div class="w-20 h-20 rounded-2xl bg-[#211f1b] text-white flex items-center justify-center mx-auto mb-6 text-3xl font-display shadow-xl relative z-10">3</div>
                        <h3 class="text-xl font-bold text-[#211f1b] mb-3">Registra pacientes</h3>
                        <p class="text-[#6b6459] leading-relaxed text-sm">Añade pacientes con historias clínicas completas.</p>
                    </div>

                    <div class="relative text-center reveal reveal-delay-4">
                        <div class="w-20 h-20 rounded-2xl bg-[#157e73] text-white flex items-center justify-center mx-auto mb-6 text-3xl font-display shadow-xl relative z-10">4</div>
                        <h3 class="text-xl font-bold text-[#211f1b] mb-3">Comienza a tratar</h3>
                        <p class="text-[#6b6459] leading-relaxed text-sm">Agenda citas, crea planes y gestiona tu práctica.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== PRICING ===== -->
        <section id="pricing" class="scroll-mt-24 py-24 lg:py-32" x-data="{ annual: false }">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="max-w-2xl mb-16 lg:mb-20 reveal">
                    <h2 class="text-4xl lg:text-5xl font-display font-bold tracking-tight text-[#211f1b] leading-[1.1] mb-6">Precios claros, sin sorpresas</h2>
                    <p class="text-lg text-[#625c51] leading-relaxed">Empieza con 14 días gratis. Cancela cuando quieras.</p>
                </div>

                <div class="flex flex-col items-center gap-2 mb-10 reveal">
                    <div class="inline-flex rounded-full border border-[#e8e4da] bg-white p-1" role="group" aria-label="Ciclo de facturación">
                        <button type="button" x-on:click="annual = false" :aria-pressed="!annual" class="px-5 py-2.5 min-h-[44px] rounded-full text-sm font-semibold transition-colors" :class="!annual ? 'bg-[#157e73] text-white' : 'text-[#6b6459] hover:text-[#211f1b]'">Mensual</button>
                        <button type="button" x-on:click="annual = true" :aria-pressed="annual" class="px-5 py-2.5 min-h-[44px] rounded-full text-sm font-semibold transition-colors" :class="annual ? 'bg-[#157e73] text-white' : 'text-[#6b6459] hover:text-[#211f1b]'">Anual <span class="opacity-80">-20%</span></button>
                    </div>
                    <p class="text-xs text-[#6b6459]" x-show="annual" x-cloak>Facturado por año. Ahorras ~$96 en Starter y ~$216 en Pro.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <div class="relative flex flex-col bg-white rounded-2xl border border-[#e8e4da] p-8 hover:border-[#211f1b]/20 transition-all duration-300 reveal reveal-delay-1">
                        <h3 class="text-xl font-bold text-[#211f1b] mb-2">Starter</h3>
                        <p class="text-sm text-[#6b6459] mb-6">Para consultorios pequeños</p>
                        <div class="mb-6">
                            <span class="text-5xl font-display tnum text-[#211f1b]"><span x-show="!annual">$39</span><span x-show="annual" x-cloak>$31</span></span>
                            <span class="text-[#6b6459]">/mes</span>
                            <p x-show="annual" x-cloak class="text-xs text-[#6b6459] mt-1">facturado $372/año</p>
                        </div>
                        <ul class="space-y-4 mb-8 flex-1">
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Hasta 5 miembros
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Hasta 500 pacientes
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Odontograma básico
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Soporte por email
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-auto block w-full text-center px-6 py-3 rounded-lg text-sm font-semibold text-[#211f1b] bg-[#f4f1ea] border border-[#e8e4da] hover:bg-[#e8e4da] transition-all">Empezar gratis</a>
                    </div>

                    <div class="relative flex flex-col bg-white rounded-2xl border-2 border-[#1f9e8f] p-8 shadow-xl shadow-[#1f9e8f]/10 scale-[1.02] reveal reveal-delay-2">
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-lg bg-[#157e73] text-white text-xs font-bold">Más popular</div>
                        <h3 class="text-xl font-bold text-[#211f1b] mb-2">Pro</h3>
                        <p class="text-sm text-[#6b6459] mb-6">Para clínicas en crecimiento</p>
                        <div class="mb-6">
                            <span class="text-5xl font-display tnum text-[#211f1b]"><span x-show="!annual">$89</span><span x-show="annual" x-cloak>$71</span></span>
                            <span class="text-[#6b6459]">/mes</span>
                            <p x-show="annual" x-cloak class="text-xs text-[#6b6459] mt-1">facturado $852/año</p>
                        </div>
                        <ul class="space-y-4 mb-8 flex-1">
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Hasta 15 miembros
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Pacientes ilimitados
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Odontograma + presupuestos
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Portal del paciente
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Exportación a PDF
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Soporte prioritario
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="mt-auto block w-full text-center px-6 py-3 rounded-lg text-sm font-semibold text-white bg-[#157e73] hover:bg-[#14645c] shadow-lg shadow-[#157e73]/20 transition-all">Empezar gratis</a>
                    </div>

                    <div class="relative flex flex-col bg-white rounded-2xl border border-[#e8e4da] p-8 hover:border-[#211f1b]/20 transition-all duration-300 reveal reveal-delay-3">
                        <h3 class="text-xl font-bold text-[#211f1b] mb-2">Enterprise</h3>
                        <p class="text-sm text-[#6b6459] mb-6">Para grupos multi-sede</p>
                        <div class="mb-6">
                            <span class="text-5xl font-display tnum text-[#211f1b]">A medida</span>
                        </div>
                        <ul class="space-y-4 mb-8 flex-1">
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Miembros ilimitados
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Multi-sede
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Integraciones personalizadas
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Gerente de cuenta dedicado
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[#625c51]">
                                <svg class="w-5 h-5 text-[#1f9e8f] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Soporte prioritario dedicado
                            </li>
                        </ul>
                        <a href="{{ $contactHref }}"{{ $contactExternal }} class="mt-auto block w-full text-center px-6 py-3 rounded-lg text-sm font-semibold text-[#211f1b] bg-[#f4f1ea] border border-[#e8e4da] hover:bg-[#e8e4da] transition-all">Contactar</a>
                        <p class="text-xs text-[#6b6459] mt-2 text-center">o escríbenos a <a href="{{ $contactHref }}"{{ $contactExternal }} class="text-[#157e73] hover:underline">{{ $contactEmail }}</a></p>
                    </div>
                </div>
                <div class="mt-8 text-center reveal">
                    <p class="text-sm text-[#6b6459]">Precios en USD. Pago manual, sin pasarela ni comisiones ocultas. <a href="#faq" x-on:click="$dispatch('open-faq', 6)" class="text-[#157e73] font-medium hover:underline">Ver cómo pagar</a></p>
                    <div class="mt-4 flex flex-wrap justify-center gap-3">
                        <span class="rounded-full border border-[#e8e4da] bg-white px-4 py-2 text-xs font-medium text-[#625c51]">Pago móvil</span>
                        <span class="rounded-full border border-[#e8e4da] bg-white px-4 py-2 text-xs font-medium text-[#625c51]">Transferencia</span>
                        <span class="rounded-full border border-[#e8e4da] bg-white px-4 py-2 text-xs font-medium text-[#625c51]">Zelle</span>
                        <span class="rounded-full border border-[#e8e4da] bg-white px-4 py-2 text-xs font-medium text-[#625c51]">Binance</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== TESTIMONIALS ===== -->
        <section id="testimonials" class="relative scroll-mt-24 py-24 lg:py-32 bg-white border-y border-[#e8e4da] overflow-hidden">
            <div class="photo-soft absolute inset-0 bg-cover bg-center opacity-[0.04] pointer-events-none" style="filter: saturate(0.7);" aria-hidden="true"></div>
            <div class="bg-motif-arch absolute left-1/2 -translate-x-1/2 bottom-[-40px] w-[900px] h-[260px] opacity-[0.05] pointer-events-none" aria-hidden="true"></div>
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="max-w-2xl mb-16 lg:mb-20 reveal">
                    <h2 class="text-4xl lg:text-5xl font-display font-bold tracking-tight text-[#211f1b] leading-[1.1]">Programa de clínicas fundadoras</h2>
                    <p class="mt-5 text-lg text-[#625c51] leading-relaxed">Estamos en beta privada y buscamos clínicas venezolanas para construir DentalFlow con nosotros.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-3 mb-10">
                    <div class="bg-[#f4f1ea] rounded-2xl p-8 border border-[#e8e4da] reveal">
                        <p class="font-bold text-[#211f1b]">Acompañamiento directo</p>
                        <p class="text-sm text-[#625c51] mt-2 leading-relaxed">Onboarding de persona a persona y soporte en español por WhatsApp o correo.</p>
                    </div>
                    <div class="bg-[#f4f1ea] rounded-2xl p-8 border border-[#e8e4da] reveal reveal-delay-1">
                        <p class="font-bold text-[#211f1b]">Precio preferencial</p>
                        <p class="text-sm text-[#625c51] mt-2 leading-relaxed">Tarifa de fundador de por vida para las primeras clínicas que se suman.</p>
                    </div>
                    <div class="bg-[#f4f1ea] rounded-2xl p-8 border border-[#e8e4da] reveal reveal-delay-2">
                        <p class="font-bold text-[#211f1b]">Influyes en el roadmap</p>
                        <p class="text-sm text-[#625c51] mt-2 leading-relaxed">Tu día a día en el consultorio define las funciones que construimos primero.</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-[#1f9e8f]/20 bg-[#1f9e8f]/5 p-6 gap-4 sm:flex sm:items-center sm:justify-between reveal">
                    <div>
                        <p class="font-semibold text-[#211f1b]">@if($waitlist) Quedan {{ $waitlist }} cupos de la beta @else Cupos limitados de la beta @endif</p>
                        <p class="text-sm text-[#625c51] mt-1">Suma tu clínica con precio preferencial de fundador y 14 días gratis, sin tarjeta de crédito.</p>
                    </div>
                    <a href="{{ route('register') }}" class="mt-4 sm:mt-0 inline-flex shrink-0 items-center justify-center px-5 py-3 rounded-lg text-sm font-semibold text-white bg-[#157e73] hover:bg-[#14645c] transition-colors">Empezar gratis</a>
                </div>

                <div class="mt-6 flex items-center gap-4 rounded-2xl border border-[#e8e4da] bg-white p-5 reveal">
                    <span class="w-11 h-11 rounded-full bg-[#157e73]/10 text-[#157e73] flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M17 20v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm11 9v-1a4 4 0 0 0-3-3.87M16 3.13A4 4 0 0 1 16 11"/></svg>
                    </span>
                    <div>
                        <p class="font-semibold text-[#211f1b]">Un equipo venezolano</p>
                        <p class="text-sm text-[#625c51] mt-0.5">Construimos DentalFlow junto a clínicas de Venezuela. Escríbenos a <a href="{{ $contactHref }}"{{ $contactExternal }} class="text-[#157e73] font-medium hover:underline">{{ $contactEmail }}</a>.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== FAQ ===== -->
        <section id="faq" class="scroll-mt-24 py-24 lg:py-32">
            <div class="max-w-3xl mx-auto px-6 lg:px-12">
                <div class="max-w-2xl mb-16 reveal">
                    <h2 class="text-4xl lg:text-5xl font-display font-bold tracking-tight text-[#211f1b] leading-[1.1]">Preguntas frecuentes</h2>
                </div>

                <div class="space-y-3" x-data="{ active: null }" x-on:open-faq.window="active = $event.detail">
                    <div class="bg-white rounded-xl border border-[#e8e4da] overflow-hidden transition-all duration-200" x-bind:class="active === 1 ? 'border-[#1f9e8f]/30' : ''">
                        <button x-on:click="active = active === 1 ? null : 1" x-bind:aria-expanded="active === 1" aria-controls="faq-panel-1" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#211f1b] pr-4">¿Hay prueba gratuita?</span>
                            <svg class="w-5 h-5 text-[#6b6459] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="faq-panel-1" x-show="active === 1" x-transition.opacity.duration.200ms>
                            <div class="px-6 pb-5 text-[#625c51] leading-relaxed">¡Sí! Ofrecemos 14 días de prueba gratuita con acceso completo a todas las funciones Pro. Sin tarjeta de crédito. Puedes cancelar cuando quieras.</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-[#e8e4da] overflow-hidden transition-all duration-200" x-bind:class="active === 2 ? 'border-[#1f9e8f]/30' : ''">
                        <button x-on:click="active = active === 2 ? null : 2" x-bind:aria-expanded="active === 2" aria-controls="faq-panel-2" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#211f1b] pr-4">¿Puedo cambiar de plan después?</span>
                            <svg class="w-5 h-5 text-[#6b6459] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="faq-panel-2" x-show="active === 2" x-transition.opacity.duration.200ms>
                            <div class="px-6 pb-5 text-[#625c51] leading-relaxed">Absolutamente. Puedes subir o bajar de plan cuando quieras. Las mejoras son inmediatas y las downgrades aplican al siguiente ciclo.</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-[#e8e4da] overflow-hidden transition-all duration-200" x-bind:class="active === 3 ? 'border-[#1f9e8f]/30' : ''">
                        <button x-on:click="active = active === 3 ? null : 3" x-bind:aria-expanded="active === 3" aria-controls="faq-panel-3" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#211f1b] pr-4">¿Mis datos están seguros?</span>
                            <svg class="w-5 h-5 text-[#6b6459] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="faq-panel-3" x-show="active === 3" x-transition.opacity.duration.200ms>
                            <div class="px-6 pb-5 text-[#625c51] leading-relaxed">Tus datos son tuyos. La conexión va cifrada (HTTPS/TLS), el acceso se controla por roles y puedes exportar historias, presupuestos y recetas a PDF o CSV cuando quieras.</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-[#e8e4da] overflow-hidden transition-all duration-200" x-bind:class="active === 4 ? 'border-[#1f9e8f]/30' : ''">
                        <button x-on:click="active = active === 4 ? null : 4" x-bind:aria-expanded="active === 4" aria-controls="faq-panel-4" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#211f1b] pr-4">¿Puedo migrar mis datos desde otro sistema?</span>
                            <svg class="w-5 h-5 text-[#6b6459] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="faq-panel-4" x-show="active === 4" x-transition.opacity.duration.200ms>
                            <div class="px-6 pb-5 text-[#625c51] leading-relaxed">Sí. Te ayudamos a migrar tus historias clínicas, citas y datos de pacientes. Contáctanos y te guiamos en el proceso.</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-[#e8e4da] overflow-hidden transition-all duration-200" x-bind:class="active === 5 ? 'border-[#1f9e8f]/30' : ''">
                        <button x-on:click="active = active === 5 ? null : 5" x-bind:aria-expanded="active === 5" aria-controls="faq-panel-5" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#211f1b] pr-4">¿Ofrecen descuento por pago anual?</span>
                            <svg class="w-5 h-5 text-[#6b6459] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="faq-panel-5" x-show="active === 5" x-transition.opacity.duration.200ms>
                            <div class="px-6 pb-5 text-[#625c51] leading-relaxed">Sí. Ahorra 20% con facturación anual. El descuento se aplica automáticamente.</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-[#e8e4da] overflow-hidden transition-all duration-200" x-bind:class="active === 6 ? 'border-[#1f9e8f]/30' : ''">
                        <button x-on:click="active = active === 6 ? null : 6" x-bind:aria-expanded="active === 6" aria-controls="faq-panel-6" class="w-full flex items-center justify-between px-6 py-5 text-left">
                            <span class="font-semibold text-[#211f1b] pr-4">¿Cómo puedo pagar desde Venezuela?</span>
                            <svg class="w-5 h-5 text-[#6b6459] flex-shrink-0 transition-transform duration-200" x-bind:class="active === 6 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="faq-panel-6" x-show="active === 6" x-transition.opacity.duration.200ms>
                            <div class="px-6 pb-5 text-[#625c51] leading-relaxed">Aceptamos pago móvil, transferencia bancaria, Zelle y Binance. Los precios están en USD; te indicamos la tasa del día al pagar. Activas tu plan el mismo día, sin pasarela ni comisiones ocultas.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== CTA ===== -->
        <section class="py-20 lg:py-28 reveal">
            <div class="max-w-5xl mx-auto px-6 lg:px-12">
                <div class="relative bg-[#211f1b] rounded-[2rem] p-14 lg:p-20 text-center text-white overflow-hidden">
                    <div class="photo-cta absolute inset-0 bg-cover bg-center opacity-30" style="filter: saturate(0.75) contrast(1.02);" aria-hidden="true"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-[#0f1513]/92 via-[#14423e]/88 to-[#211f1b]/95" aria-hidden="true"></div>
                    <div class="absolute top-0 right-0 w-72 h-72 bg-[#1f9e8f]/20 rounded-full blur-3xl -translate-y-1/3 translate-x-1/4" aria-hidden="true"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#c3672f]/20 rounded-full blur-3xl translate-y-1/3 -translate-x-1/4" aria-hidden="true"></div>
                    <div class="bg-motif-tooth absolute -right-8 -bottom-16 w-[240px] h-[320px] opacity-[0.10] pointer-events-none" aria-hidden="true"></div>

                    <div class="relative z-10">
                        <h2 class="text-3xl lg:text-5xl font-display leading-[1.1] mb-6">¿Listo para modernizar tu clínica?</h2>
                        <p class="text-white/70 text-lg mb-10 max-w-xl mx-auto leading-relaxed">Empieza tu prueba gratuita de 14 días. Sin tarjeta de crédito.</p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-lg text-base font-semibold text-[#211f1b] bg-white hover:bg-[#e8e4da] shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                                Empezar gratis
                                <svg class="ml-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="#features" class="inline-flex items-center justify-center px-8 py-4 rounded-lg text-base font-semibold text-white bg-white/10 border border-white/20 hover:bg-white/20 backdrop-blur-sm transition-all duration-200">
                                <svg class="mr-2 size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5h6v6H4V5Zm10 0h6v6h-6V5ZM4 13h6v6H4v-6Zm10 0h6v6h-6v-6Z"/></svg>
                                Ver funciones
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        </main>

        <!-- ===== FOOTER ===== -->
        <footer class="border-t border-[#e8e4da] py-16">
            <div class="max-w-7xl mx-auto px-6 lg:px-12">
                <div class="grid md:grid-cols-4 gap-8 mb-12">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="relative flex items-center justify-center w-9 h-9">
                                <span class="absolute inset-0 bg-[#1f9e8f] rounded-xl rotate-6"></span>
                                <span class="absolute inset-0 bg-[#211f1b] rounded-xl -rotate-3"></span>
                                <span class="relative text-white font-bold text-xs tracking-tight z-10">DF</span>
                            </span>
                            <span class="text-lg font-bold text-[#211f1b]">DentalFlow</span>
                        </div>
                        <p class="text-sm text-[#6b6459] max-w-sm leading-relaxed">Plataforma moderna de gestión dental. Potenciando clínicas con herramientas digitales para una mejor atención al paciente.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#211f1b] mb-4">Producto</h3>
                        <ul class="space-y-3">
                            <li><a href="#features" class="text-sm text-[#6b6459] hover:text-[#157e73] transition-colors">Funciones</a></li>
                            <li><a href="#pricing" class="text-sm text-[#6b6459] hover:text-[#157e73] transition-colors">Planes</a></li>
                            <li><a href="#faq" class="text-sm text-[#6b6459] hover:text-[#157e73] transition-colors">FAQ</a></li>
                            <li><a href="#testimonials" class="text-sm text-[#6b6459] hover:text-[#157e73] transition-colors">Fundadoras</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#211f1b] mb-4">Compañía</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ route('login') }}" class="text-sm text-[#6b6459] hover:text-[#157e73] transition-colors">Iniciar sesión</a></li>
                            <li><a href="{{ $contactHref }}"{{ $contactExternal }} class="text-sm text-[#6b6459] hover:text-[#157e73] transition-colors">Contacto</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-[#e8e4da] pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-[#6b6459]">&copy; {{ date('Y') }} DentalFlow. Todos los derechos reservados.</p>
                    <div class="flex gap-6">
                        <a href="{{ route('legal.privacy') }}" class="text-sm text-[#6b6459] hover:text-[#157e73] transition-colors">Privacidad</a>
                        <a href="{{ route('legal.terms') }}" class="text-sm text-[#6b6459] hover:text-[#157e73] transition-colors">Términos</a>
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
