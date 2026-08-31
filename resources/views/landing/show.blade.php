<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->landing_title ?? $clinic->name }}</title>
    <meta name="description" content="{{ $setting->landing_description }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: {{ $setting->primary_color }};
            --secondary: {{ $setting->secondary_color }};
        }
        .bg-primary { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
        .bg-secondary { background-color: var(--secondary); }
    </style>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                @if($setting->landing_logo)
                    <img src="{{ Storage::url($setting->landing_logo) }}" alt="Logo" class="h-12">
                @endif
                <h1 class="text-2xl font-bold text-primary">{{ $setting->landing_title ?? 'Clínica Dental' }}</h1>
            </div>
            <nav class="hidden md:flex space-x-6">
                <a href="#servicios" class="text-gray-600 hover:text-primary">Servicios</a>
                <a href="#contacto" class="text-gray-600 hover:text-primary">Contacto</a>
                <a href="{{ route('landing.book', ['clinic' => $clinic->id]) }}"
                    class="bg-primary text-white px-4 py-2 rounded-lg hover:opacity-90 font-medium">
                    Agendar Cita
                </a>
            </nav>
        </div>
    </header>

    <section class="relative bg-gradient-to-r from-primary to-secondary text-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">{{ $setting->landing_title ?? 'Tu Sonrisa, Nuestra Prioridad' }}</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">{{ $setting->landing_description ?? 'Atención dental profesional' }}</p>
            <a href="{{ route('landing.book', ['clinic' => $clinic->id]) }}"
                class="inline-block bg-white text-primary font-semibold px-8 py-3 rounded-lg hover:bg-gray-100">
                Agendar Cita
            </a>
        </div>
    </section>

    <section id="servicios" class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-12">Nuestros Servicios</h3>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-4xl mb-4">🦷</div>
                    <h4 class="text-xl font-semibold mb-2">Limpieza Dental</h4>
                    <p class="text-gray-600">Limpieza profesional para tu salud bucal</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-4xl mb-4">✨</div>
                    <h4 class="text-xl font-semibold mb-2">Blanqueamiento</h4>
                    <p class="text-gray-600">Recupera el brillo natural de tu sonrisa</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-4xl mb-4">🔧</div>
                    <h4 class="text-xl font-semibold mb-2">Ortodoncia</h4>
                    <p class="text-gray-600">Corrección dental con la mejor tecnología</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contacto" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-12">Contáctanos</h3>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    @if($setting->landing_address)
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">📍</span>
                        <div>
                            <h4 class="font-semibold">Dirección</h4>
                            <p class="text-gray-600">{{ $setting->landing_address }}</p>
                        </div>
                    </div>
                    @endif
                    @if($setting->landing_phone)
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">📞</span>
                        <div>
                            <h4 class="font-semibold">Teléfono</h4>
                            <p class="text-gray-600">{{ $setting->landing_phone }}</p>
                        </div>
                    </div>
                    @endif
                    @if($setting->landing_email)
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">✉️</span>
                        <div>
                            <h4 class="font-semibold">Email</h4>
                            <p class="text-gray-600">{{ $setting->landing_email }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="flex space-x-4">
                    @if($setting->landing_whatsapp)
                    <a href="https://wa.me/{{ $setting->landing_whatsapp }}" target="_blank" class="bg-green-500 text-white p-3 rounded-full hover:bg-green-600">
                        <span class="text-2xl">💬</span>
                    </a>
                    @endif
                    @if($setting->landing_facebook)
                    <a href="{{ $setting->landing_facebook }}" target="_blank" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700">
                        <span class="text-2xl">📘</span>
                    </a>
                    @endif
                    @if($setting->landing_instagram)
                    <a href="{{ $setting->landing_instagram }}" target="_blank" class="bg-pink-500 text-white p-3 rounded-full hover:bg-pink-600">
                        <span class="text-2xl">📷</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} {{ $setting->landing_title ?? 'Clínica Dental' }}. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
