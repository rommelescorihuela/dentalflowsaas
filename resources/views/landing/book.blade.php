<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita | {{ $setting->landing_title ?? $clinic->name }}</title>
    <meta name="description" content="Agenda tu cita dental online">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: {{ $setting->primary_color ?? '#06b6d4' }};
            --secondary: {{ $setting->secondary_color ?? '#0891b2' }};
        }
        .bg-primary { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
        .bg-secondary { background-color: var(--secondary); }
        .hover\:bg-primary:hover { background-color: var(--primary); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white shadow-sm">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                @if($setting->landing_logo)
                    <img src="{{ Storage::url($setting->landing_logo) }}" alt="Logo" class="h-10">
                @endif
                <h1 class="text-xl font-bold text-primary">{{ $setting->landing_title ?? 'Clínica Dental' }}</h1>
            </div>
            <a href="{{ route('landing.show', ['clinic' => $clinic->id]) }}" class="text-gray-500 hover:text-primary text-sm">
                &larr; Volver
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-10">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Agendar Cita</h2>
        <p class="text-gray-500 mb-8">Completa el formulario y nos pondremos en contacto para confirmar.</p>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('landing.book.store', ['clinic' => $clinic->id]) }}" method="POST" class="bg-white rounded-2xl shadow-lg p-6 space-y-6">
            @csrf

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Datos Personales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="Tu nombre">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="tucorreo@ejemplo.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="+56 9 1234 5678">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Servicio y Horario</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Procedimiento *</label>
                        <select name="procedure_price_id" id="procedure" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            <option value="">Selecciona...</option>
                            @foreach($procedures as $procedure)
                                <option value="{{ $procedure->id }}" {{ old('procedure_price_id') == $procedure->id ? 'selected' : '' }}>
                                    {{ $procedure->procedure_name }}@if($procedure->price) — ${{ number_format($procedure->price, 0, ',', '.') }}@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input type="date" name="date" id="date" value="{{ old('date') }}" required min="{{ now()->toDateString() }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                        <select name="time" id="time" required disabled
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-cyan-500 focus:border-transparent disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="">Selecciona fecha primero</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                <textarea name="notes" rows="3"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                    placeholder="¿Algo que debamos saber?">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-primary text-white font-semibold py-3 px-6 rounded-xl hover:opacity-90 transition-opacity">
                    Confirmar Reserva
                </button>
            </div>
        </form>
    </main>

    <footer class="bg-gray-800 text-white py-6 mt-10">
        <div class="max-w-3xl mx-auto px-4 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} {{ $setting->landing_title ?? 'Clínica Dental' }}
        </div>
    </footer>

    <script>
        const dateInput = document.getElementById('date');
        const timeSelect = document.getElementById('time');
        const procedureSelect = document.getElementById('procedure');
        const clinicId = @json($clinic->id);

        async function loadSlots() {
            const date = dateInput.value;
            if (!date) {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="">Selecciona fecha primero</option>';
                return;
            }

            const procedureId = procedureSelect.value;
            const url = `/landing/${clinicId}/book/slots?date=${date}&procedure_id=${procedureId}`;

            try {
                const response = await fetch(url);
                const data = await response.json();

                timeSelect.innerHTML = '<option value="">Selecciona hora...</option>';
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSelect.appendChild(option);
                });

                timeSelect.disabled = data.slots.length === 0;
                if (data.slots.length === 0) {
                    timeSelect.innerHTML = '<option value="">No hay horas disponibles</option>';
                }
            } catch (e) {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="">Error al cargar horas</option>';
            }
        }

        dateInput.addEventListener('change', loadSlots);
        procedureSelect.addEventListener('change', loadSlots);
    </script>
</body>
</html>
