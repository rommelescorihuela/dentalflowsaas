<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Agendada | {{ $setting->landing_title ?? $clinic->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: {{ $setting->primary_color ?? '#06b6d4' }};
            --secondary: {{ $setting->secondary_color ?? '#0891b2' }};
        }
        .bg-primary { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-2">¡Cita Agendada!</h2>
        <p class="text-gray-500 mb-6">Hemos recibido tu solicitud. La clínica te contactará para confirmar.</p>

        <div class="bg-gray-50 rounded-xl p-4 text-left mb-6">
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Servicio</span>
                <span class="text-sm font-medium text-gray-900">{{ $procedure->procedure_name }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Fecha</span>
                <span class="text-sm font-medium text-gray-900">{{ $appointment->start_time->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-sm text-gray-500">Hora</span>
                <span class="text-sm font-medium text-gray-900">{{ $appointment->start_time->format('H:i') }}</span>
            </div>
        </div>

        <a href="{{ route('landing.show', ['clinic' => $clinic->id]) }}"
            class="inline-block bg-primary text-white font-semibold py-3 px-6 rounded-xl hover:opacity-90 transition-opacity">
            Volver al inicio
        </a>
    </div>
</body>
</html>
