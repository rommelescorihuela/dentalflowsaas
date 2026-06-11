<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Presupuesto - {{ $budget->patient->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-cyan-50 via-white to-primary-50/50 font-sans antialiased min-h-screen">
    <!-- Decorative background elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
    </div>

    <div class="relative min-h-screen">
        <!-- Header -->
        <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100">
            <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <div>
                    <a href="{{ URL::signedRoute('portal.dashboard', ['patient' => $budget->patient]) }}"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-primary-600 transition-colors mb-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Volver al portal
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Detalle del Presupuesto</h1>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold shadow-lg
                    @if($budget->status === 'accepted') bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 shadow-green-200
                    @elseif($budget->status === 'sent') bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-700 shadow-yellow-200
                    @elseif($budget->status === 'rejected') bg-gradient-to-r from-red-100 to-rose-100 text-red-700 shadow-red-200
                    @else bg-gradient-to-r from-gray-100 to-slate-100 text-gray-700 shadow-gray-200 @endif">
                    @switch($budget->status)
                        @case('accepted') Aceptado @break
                        @case('sent') Enviado @break
                        @case('rejected') Rechazado @break
                        @case('draft') Borrador @break
                        @default {{ ucfirst($budget->status) }}
                    @endswitch
                </span>
            </div>
        </header>

        <main class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Budget Info -->
            <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Información del Presupuesto</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Paciente</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $budget->patient->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Emisión</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $budget->created_at->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Válido Hasta</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($budget->expires_at)
                                    {{ $budget->expires_at->format('d/m/Y') }}
                                    @if($budget->expires_at->isPast() && $budget->status === 'sent')
                                    <span class="ml-2 text-red-600 text-xs font-semibold">(Vencido)</span>
                                    @endif
                                @else
                                    Sin fecha de vencimiento
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total</dt>
                            <dd class="mt-1 text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                                ${{ number_format($budget->total, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>
                    @if($budget->notes)
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Notas</dt>
                        <dd class="mt-2 text-sm text-gray-700 bg-gray-50 rounded-xl p-4">{{ $budget->notes }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Budget Items -->
            <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                    <h3 class="text-lg font-bold text-gray-900">Detalle de Tratamientos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tratamiento</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($budget->items as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->treatment_name }}</div>
                                    @if($item->procedurePrice)
                                    <div class="text-xs text-gray-500 mt-1">{{ $item->procedurePrice->category ?? '' }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-center">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">${{ number_format($item->cost, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">${{ number_format($item->cost * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No hay tratamientos en este presupuesto.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-900">Total:</td>
                                <td class="px-6 py-4 text-right text-lg font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                                    ${{ number_format($budget->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            @if($budget->status === 'sent')
            <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
                    <h3 class="text-lg font-bold text-gray-900">¿Qué deseas hacer con este presupuesto?</h3>
                </div>
                <div class="p-6 flex flex-col sm:flex-row gap-4 justify-center">
                    <form action="{{ URL::signedRoute('portal.budgets.accept', ['budget' => $budget]) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Aceptar Presupuesto
                        </button>
                    </form>
                    <form action="{{ URL::signedRoute('portal.budgets.reject', ['budget' => $budget]) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 border border-red-200 rounded-xl shadow-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Rechazar Presupuesto
                        </button>
                    </form>
                </div>
            </div>
            @elseif($budget->status === 'accepted')
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-6 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-green-800">Presupuesto Aceptado</h3>
                <p class="text-sm text-green-700 mt-2">Has aceptado este presupuesto. Tu clínica se pondrá en contacto para coordinar el tratamiento.</p>
            </div>
            @elseif($budget->status === 'rejected')
            <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-2xl p-6 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-red-800">Presupuesto Rechazado</h3>
                <p class="text-sm text-red-700 mt-2">Has rechazado este presupuesto. Si deseas más información, contacta a tu clínica.</p>
            </div>
            @endif
        </main>
    </div>
</body>

</html>
