<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->landing_title ?? $clinic->name }}</title>
    <meta name="description" content="{{ $setting->landing_description }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: {{ $setting->primary_color ?? '#1f9e8f' }};
            --secondary: {{ $setting->secondary_color ?? '#157e73' }};
        }
        body { font-family: 'Onest', ui-sans-serif, system-ui, sans-serif; }
        .bg-primary { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
        .border-primary { border-color: var(--primary); }
        .bg-secondary { background-color: var(--secondary); }
        .hover\:bg-primary:hover { background-color: var(--primary); }
        .hover\:text-primary:hover { color: var(--primary); }
    </style>
</head>
<body class="bg-[#faf9f6] text-[#23282a] antialiased">
    <header class="sticky top-0 z-40 bg-[#faf9f6]/85 backdrop-blur-md border-b border-[#e8e4da]">
        <div class="max-w-6xl mx-auto px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($setting->landing_logo)
                    <img src="{{ Storage::url($setting->landing_logo) }}" alt="{{ $setting->landing_title ?? $clinic->name }}" class="h-10 w-auto rounded-lg">
                @endif
                <h1 class="text-xl font-bold text-primary tracking-tight">{{ $setting->landing_title ?? 'Clínica Dental' }}</h1>
            </div>
            <nav class="hidden md:flex items-center gap-7">
                <a href="#servicios" class="text-sm text-[#625c51] hover:text-primary transition-colors">Servicios</a>
                <a href="#contacto" class="text-sm text-[#625c51] hover:text-primary transition-colors">Contacto</a>
                <a href="{{ route('landing.book', ['clinic' => $clinic->id]) }}"
                    class="bg-primary text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-sm hover:opacity-90 transition-opacity">
                    Agendar Cita
                </a>
            </nav>
            <a href="{{ route('landing.book', ['clinic' => $clinic->id]) }}" class="md:hidden bg-primary text-white px-4 py-2 rounded-lg font-semibold text-sm">Agendar</a>
        </div>
    </header>

    <section class="relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 right-0 w-[520px] h-[520px] rounded-full blur-3xl opacity-[0.12]" style="background: var(--primary);"></div>
            <div class="absolute bottom-0 -left-24 w-[380px] h-[380px] rounded-full blur-3xl opacity-[0.10]" style="background: var(--secondary);"></div>
        </div>
        <div class="max-w-6xl mx-auto px-5 py-20 md:py-28 relative">
            <div class="max-w-2xl">
                <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-primary mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    Atención dental profesional
                </p>
                <h2 class="text-4xl md:text-6xl font-bold leading-[1.05] tracking-tight text-[#211f1b] mb-6">
                    {{ $setting->landing_title ?? 'Tu sonrisa, nuestra prioridad' }}
                </h2>
                <p class="text-lg text-[#625c51] leading-relaxed mb-9 max-w-xl">
                    {{ $setting->landing_description ?? 'Atención dental profesional con tecnología moderna y un trato cercano.' }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('landing.book', ['clinic' => $clinic->id]) }}"
                        class="inline-flex items-center justify-center bg-primary text-white font-semibold px-7 py-3.5 rounded-xl shadow-sm hover:opacity-90 transition-opacity">
                        Agendar cita
                    </a>
                    <a href="#servicios" class="inline-flex items-center justify-center bg-white text-[#211f1b] font-semibold px-7 py-3.5 rounded-xl border border-[#e8e4da] hover:border-primary transition-colors">
                        Ver servicios
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="servicios" class="py-20 md:py-24 bg-white border-y border-[#e8e4da]">
        <div class="max-w-6xl mx-auto px-5">
            <div class="max-w-xl mb-14">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary mb-4">Servicios</p>
                <h3 class="text-3xl md:text-4xl font-bold tracking-tight text-[#211f1b]">Tratamientos para toda la familia</h3>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-[#faf9f6] rounded-2xl border border-[#e8e4da] p-7 hover:border-primary/40 transition-colors">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 3c-2.5 0-3.5 1.5-5 1.5S4.5 3.6 3.9 5.2c-.9 2.4.1 5.4 1.6 7.3.8 1 1.3 2.3 1.4 3.6.1 1.5.4 3.4 1.6 3.4 1.1 0 1.1-1.4 1.3-3 .2-1.3.7-2 1.2-2s1 .7 1.2 2c.2 1.6.2 3 1.3 3 1.2 0 1.5-1.9 1.6-3.4.1-1.3.6-2.6 1.4-3.6 1.5-1.9 2.5-4.9 1.6-7.3C18.5 3.6 17.5 4.5 17 4.5s-2.5-1.5-5-1.5Z"/></svg>
                    </div>
                    <h4 class="text-lg font-bold text-[#211f1b] mb-2">Limpieza dental</h4>
                    <p class="text-sm text-[#625c51] leading-relaxed">Profilaxis profesional para mantener tu salud bucal al día.</p>
                </div>
                <div class="bg-[#faf9f6] rounded-2xl border border-[#e8e4da] p-7 hover:border-primary/40 transition-colors">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 3v4m0 0 3-1.5M12 7 9 5.5M5 12l3.5 2M19 12l-3.5 2M7 20l3-4m7 4-3-4"/></svg>
                    </div>
                    <h4 class="text-lg font-bold text-[#211f1b] mb-2">Blanqueamiento</h4>
                    <p class="text-sm text-[#625c51] leading-relaxed">Recupera el brillo natural de tu sonrisa con resultados visibles.</p>
                </div>
                <div class="bg-[#faf9f6] rounded-2xl border border-[#e8e4da] p-7 hover:border-primary/40 transition-colors">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 8h16M4 8v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8M4 8l2-3h12l2 3M8 12h.01M12 12h.01M16 12h.01"/></svg>
                    </div>
                    <h4 class="text-lg font-bold text-[#211f1b] mb-2">Ortodoncia</h4>
                    <p class="text-sm text-[#625c51] leading-relaxed">Corrección dental con tecnología actual y seguimiento continuo.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contacto" class="py-20 md:py-24">
        <div class="max-w-6xl mx-auto px-5">
            <div class="grid md:grid-cols-2 gap-12 items-start">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary mb-4">Contacto</p>
                    <h3 class="text-3xl md:text-4xl font-bold tracking-tight text-[#211f1b] mb-8">Estamos para ayudarte</h3>
                    <div class="space-y-5">
                        @if($setting->landing_address)
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 21s7-5.3 7-11a7 7 0 1 0-14 0c0 5.7 7 11 7 11Zm0-8.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-[#211f1b]">Dirección</h4>
                                <p class="text-[#625c51]">{{ $setting->landing_address }}</p>
                            </div>
                        </div>
                        @endif
                        @if($setting->landing_phone)
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 5.5C3 4.7 3.7 4 4.5 4h3c.7 0 1.3.5 1.5 1.2l.7 2.8c.1.6-.1 1.2-.6 1.5l-1.2.9a12 12 0 0 0 5.7 5.7l.9-1.2c.4-.5 1-.7 1.5-.6l2.8.7c.7.2 1.2.8 1.2 1.5v3c0 .8-.7 1.5-1.5 1.5A16.5 16.5 0 0 1 3 5.5Z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-[#211f1b]">Teléfono</h4>
                                <p class="text-[#625c51]">{{ $setting->landing_phone }}</p>
                            </div>
                        </div>
                        @endif
                        @if($setting->landing_email)
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 7.5A2.5 2.5 0 0 1 5.5 5h13A2.5 2.5 0 0 1 21 7.5v9a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 16.5v-9Zm.8-.6 8.2 6 8.2-6"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-[#211f1b]">Email</h4>
                                <p class="text-[#625c51]">{{ $setting->landing_email }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-[#e8e4da] p-8 shadow-[0_1px_2px_rgba(35,40,42,0.04),0_18px_40px_-24px_rgba(20,80,75,0.3)]">
                    <h4 class="text-xl font-bold text-[#211f1b] mb-2">Reserva tu próxima cita</h4>
                    <p class="text-sm text-[#625c51] mb-6">Elige el día y la hora que mejor te convenga. Te confirmamos por correo.</p>
                    <a href="{{ route('landing.book', ['clinic' => $clinic->id]) }}"
                        class="block w-full text-center bg-primary text-white font-semibold py-3.5 rounded-xl hover:opacity-90 transition-opacity">
                        Agendar cita online
                    </a>
                    <div class="flex gap-3 mt-6">
                        @if($setting->landing_whatsapp)
                        <a href="https://wa.me/{{ $setting->landing_whatsapp }}" target="_blank" class="flex-1 text-center bg-[#faf9f6] border border-[#e8e4da] rounded-xl py-2.5 text-sm font-medium text-[#625c51] hover:border-primary hover:text-primary transition-colors">WhatsApp</a>
                        @endif
                        @if($setting->landing_facebook)
                        <a href="{{ $setting->landing_facebook }}" target="_blank" class="flex-1 text-center bg-[#faf9f6] border border-[#e8e4da] rounded-xl py-2.5 text-sm font-medium text-[#625c51] hover:border-primary hover:text-primary transition-colors">Facebook</a>
                        @endif
                        @if($setting->landing_instagram)
                        <a href="{{ $setting->landing_instagram }}" target="_blank" class="flex-1 text-center bg-[#faf9f6] border border-[#e8e4da] rounded-xl py-2.5 text-sm font-medium text-[#625c51] hover:border-primary hover:text-primary transition-colors">Instagram</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-[#211f1b] text-[#b0a99a] py-8">
        <div class="max-w-6xl mx-auto px-5 text-center text-sm">
            &copy; {{ date('Y') }} {{ $setting->landing_title ?? 'Clínica Dental' }}. Todos los derechos reservados.
        </div>
    </footer>
</body>
</html>
