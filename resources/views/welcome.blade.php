<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'DentalFlowSaaS') }} - Modern Dental Management</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-gray-50 text-gray-900 selection:bg-blue-500 selection:text-white">

    <!-- Navigation -->
    <nav class="w-full py-6 px-6 lg:px-12 flex justify-between items-center max-w-7xl mx-auto">
        <div class="text-2xl font-bold text-blue-600 flex items-center gap-2">
            <span class="bg-blue-600 text-white rounded-lg p-1.5 ">
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
                        class="font-semibold text-gray-600 hover:text-blue-600 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-blue-600 transition">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-full transition shadow-lg shadow-blue-200">Get
                            Started</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-6 lg:px-12 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block py-1 px-3 rounded-full bg-blue-100 text-blue-600 text-sm font-semibold mb-6">v1.0
                Now Available</span>
            <h1 class="text-5xl lg:text-7xl font-bold leading-tight mb-6">
                Manage your clinic <br>
                <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Effortlessly.</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 leading-relaxed max-w-lg">
                The all-in-one SaaS platform for modern dental professionals. Patient records, smart scheduling, and
                billing in one beautiful interface.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('register') }}"
                    class="bg-blue-600 text-white font-bold py-4 px-8 rounded-full text-center hover:bg-blue-700 transition shadow-xl shadow-blue-200">
                    Start Free Trial
                </a>
                <a href="#features"
                    class="bg-white text-gray-700 border border-gray-200 font-bold py-4 px-8 rounded-full text-center hover:bg-gray-50 transition">
                    Learn More
                </a>
            </div>
        </div>

        <!-- Hero Image / Visual -->
        <div class="relative">
            <div
                class="absolute -inset-4 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-2xl opacity-20 blur-2xl animate-pulse">
            </div>
            <div
                class="relative bg-white border border-gray-200 rounded-2xl shadow-2xl overflow-hidden aspect-[4/3] flex items-center justify-center">
                <div class="text-center p-8">
                    <div
                        class="bg-blue-50 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0h18M5.25 12h13.5h-13.5Zm1 5.25h13.5h-13.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Smart Scheduler</h3>
                    <p class="text-gray-500">Intelligent appointment booking engine.</p>
                    <!-- Fake UI Elements -->
                    <div class="mt-6 space-y-3 opacity-50 select-none">
                        <div class="h-4 bg-gray-100 rounded w-3/4 mx-auto"></div>
                        <div class="h-4 bg-gray-100 rounded w-1/2 mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold mb-4">Everything you need to run your practice</h2>
                <p class="text-gray-600 text-lg">Focus on your patients, we'll handle the administration.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div
                    class="p-8 rounded-2xl bg-gray-50 hover:bg-blue-50 transition border border-transparent hover:border-blue-100 group">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm text-blue-600 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Patient Records</h3>
                    <p class="text-gray-600 leading-relaxed">Secure digital charts with medical history, allergies, and
                        treatment plans accessible anywhere.</p>
                </div>

                <!-- Feature 2 -->
                <div
                    class="p-8 rounded-2xl bg-gray-50 hover:bg-blue-50 transition border border-transparent hover:border-blue-100 group">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm text-blue-600 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Interactive Odontogram</h3>
                    <p class="text-gray-600 leading-relaxed">Visual treatment planning with our state-of-the-art
                        3D-style tooth chart interface.</p>
                </div>

                <!-- Feature 3 -->
                <div
                    class="p-8 rounded-2xl bg-gray-50 hover:bg-blue-50 transition border border-transparent hover:border-blue-100 group">
                    <div
                        class="w-12 h-12 bg-white rounded-xl shadow-sm text-blue-600 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Seamless Billing</h3>
                    <p class="text-gray-600 leading-relaxed">Generate invoices, track payments, and manage insurance
                        claims with automated workflows.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20">
        <div class="max-w-5xl mx-auto px-6 lg:px-12">
            <div class="bg-blue-600 rounded-3xl p-12 lg:p-20 text-center text-white relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-blue-500 opacity-20 transform -skew-y-12 scale-150">
                </div>
                <div class="relative z-10">
                    <h2 class="text-3xl lg:text-4xl font-bold mb-6">Ready to modernize your clinic?</h2>
                    <p class="text-blue-100 text-lg mb-10 max-w-xl mx-auto">Join hundreds of dentists who trust
                        DentalFlow to manage their practice. No credit card required for trial.</p>
                    <a href="{{ route('register') }}"
                        class="bg-white text-blue-600 font-bold py-4 px-10 rounded-full hover:bg-gray-100 transition shadow-xl">
                        Get Started Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2 font-bold text-gray-700">
                <span class="text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </span>
                DentalFlowSaaS
            </div>
            <div class="text-sm text-gray-500">
                &copy; {{ date('Y') }} DentalFlowSaaS. All rights reserved.
            </div>
        </div>
    </footer>
</body>

</html>