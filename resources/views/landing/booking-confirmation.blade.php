<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Agendada | {{ $setting->landing_title ?? $clinic->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: {{ $setting->primary_color ?? '#1f9e8f' }};
            --secondary: {{ $setting->secondary_color ?? '#1f9e8f' }};
        }
        .bg-primary { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
    </style>
</head>
<body class="bg-[#faf9f6] min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>

        <h2 class="text-2xl font-bold text-[#211f1b] mb-2">¡Cita Agendada!</h2>
        <p class="text-[#847d6f] mb-6">Hemos recibido tu solicitud. La clínica te contactará para confirmar.</p>

        <div class="bg-[#faf9f6] rounded-xl p-4 text-left mb-6">
            <div class="flex justify-between py-2 border-b border-[#f4f1ea]">
                <span class="text-sm text-[#847d6f]">Servicio</span>
                <span class="text-sm font-medium text-[#211f1b]">{{ $procedure->procedure_name }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-[#f4f1ea]">
                <span class="text-sm text-[#847d6f]">Fecha</span>
                <span class="text-sm font-medium text-[#211f1b]">{{ $appointment->start_time->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-sm text-[#847d6f]">Hora</span>
                <span class="text-sm font-medium text-[#211f1b]">{{ $appointment->start_time->format('H:i') }}</span>
            </div>
        </div>

        <a href="{{ route('landing.show', ['clinic' => $clinic->id]) }}"
            class="inline-block bg-primary text-white font-semibold py-3 px-6 rounded-xl hover:opacity-90 transition-opacity">
            Volver al inicio
        </a>
    </div>
</body>
</html>
