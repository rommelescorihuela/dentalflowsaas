<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'DentalFlow') }} — Modern Dental Management</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|noto-sans:300,400,500,700&display=swap" rel="stylesheet" />
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

<body class="antialiased bg-cyan-50/30 text-gray-900 selection:bg-primary-500 selection:text-white">

    <nav class="w-full py-6 px-6 lg:px-12 flex justify-between items-center max-w-7xl mx-auto">
        <div class="text-2xl font-bold text-primary-600 flex items-center gap-2">
            <span class="bg-primary-600 text-white rounded-lg p-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </span>
            DentalFlow
        </div>
        <div class="flex items-center gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="font-medium text-gray-600 hover:text-primary-600 transition-colors duration-200">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-medium text-gray-600 hover:text-primary-600 transition-colors duration-200">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors duration-200 shadow-md shadow-primary-500/20">
                            Get Started
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <section class="max-w-7xl mx-auto px-6 lg:px-12 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block py-1.5 px-4 rounded-full bg-primary-50 text-primary-700 text-sm font-semibold mb-6 border border-primary-100">
                v1.0 Now Available
            </span>
            <h1 class="text-5xl lg:text-7xl font-bold leading-tight mb-6 font-heading">
                Manage your clinic <br>
                <span class="gradient-text">Effortlessly.</span>
            </h1>
            <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-lg">
                The all-in-one SaaS platform for modern dental professionals. Patient records, smart scheduling, and
                billing in one beautiful interface.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('register') }}"
                    class="bg-primary-600 text-white font-semibold py-4 px-8 rounded-lg text-center hover:bg-primary-700 transition-colors duration-200 shadow-lg shadow-primary-500/20">
                    Start Free Trial
                </a>
                <a href="#features"
                    class="bg-white text-gray-700 border border-gray-200 font-semibold py-4 px-8 rounded-lg text-center hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200">
                    Learn More
                </a>
            </div>
        </div>

        <div class="relative">
            <div class="absolute -inset-4 bg-gradient-to-r from-primary-400 to-primary-500 rounded-2xl opacity-15 blur-2xl">
            </div>
            <div
                class="relative bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden aspect-[4/3] flex items-center justify-center">
                <div class="text-center p-8">
                    <div
                        class="bg-primary-50 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-primary-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0h18M5.25 12h13.5h-13.5Zm1 5.25h13.5h-13.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2 font-heading">Smart Scheduler</h3>
                    <p class="text-gray-500">Intelligent appointment booking engine.</p>
                    <div class="mt-6 space-y-3 opacity-40 select-none">
                        <div class="h-4 bg-gray-100 rounded w-3/4 mx-auto"></div>
                        <div class="h-4 bg-gray-100 rounded w-1/2 mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold mb-4 font-heading">Everything you need to run your practice</h2>
                <p class="text-gray-600 text-lg">Focus on your patients, we'll handle the administration.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div
                    class="p-8 rounded-2xl bg-gray-50 hover:bg-primary-50/50 transition-colors duration-200 border border-transparent hover:border-primary-100 group cursor-pointer">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm text-primary-600 flex items-center justify-center mb-6 group-hover:scale-105 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 font-heading">Patient Records</h3>
                    <p class="text-gray-600 leading-relaxed">Secure digital charts with medical history, allergies, and
                        treatment plans accessible anywhere.</p>
                </div>

                <div
                    class="p-8 rounded-2xl bg-gray-50 hover:bg-primary-50/50 transition-colors duration-200 border border-transparent hover:border-primary-100 group cursor-pointer">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm text-primary-600 flex items-center justify-center mb-6 group-hover:scale-105 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 font-heading">Interactive Odontogram</h3>
                    <p class="text-gray-600 leading-relaxed">Visual treatment planning with our state-of-the-art
                        3D-style tooth chart interface.</p>
                </div>

                <div
                    class="p-8 rounded-2xl bg-gray-50 hover:bg-primary-50/50 transition-colors duration-200 border border-transparent hover:border-primary-100 group cursor-pointer">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm text-primary-600 flex items-center justify-center mb-6 group-hover:scale-105 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 font-heading">Seamless Billing</h3>
                    <p class="text-gray-600 leading-relaxed">Generate invoices, track payments, and manage insurance
                        claims with automated workflows.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="max-w-5xl mx-auto px-6 lg:px-12">
            <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-3xl p-12 lg:p-20 text-center text-white relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-primary-500 opacity-10 transform -skew-y-12 scale-150">
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
                    <h2 class="text-3xl lg:text-4xl font-bold mb-6 font-heading">Ready to modernize your clinic?</h2>
                    <p class="text-primary-100 text-lg mb-10 max-w-xl mx-auto">Join hundreds of dentists who trust
                        DentalFlow to manage their practice. No credit card required for trial.</p>
                    <a href="{{ route('register') }}"
                        class="bg-white text-primary-700 font-semibold py-4 px-10 rounded-lg hover:bg-primary-50 transition-colors duration-200 shadow-xl">
                        Get Started Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-50 border-t border-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2 font-semibold text-gray-700">
                <span class="text-primary-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </span>
                DentalFlow
            </div>
            <div class="text-sm text-gray-500">
                &copy; {{ date('Y') }} DentalFlow. All rights reserved.
            </div>
        </div>
    </footer>

</html>
