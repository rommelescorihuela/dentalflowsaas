<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Presupuesto — {{ $budget->patient->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800|work-sans:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --teal: #0D9488; --gold: #F59E0B; --ink: #292524; }
        body { font-family: 'Work Sans', sans-serif; background: #FFFBF5; color: var(--ink); }
        .font-display { font-family: 'Outfit', sans-serif; letter-spacing: -0.02em; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-16px); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: 0.01ms !important; } }
    </style>
</head>

<body class="bg-gradient-to-br from-cyan-50 via-white to-primary-50/50 font-sans antialiased min-h-screen">
    <!-- Decorative background elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
    </div>

    <div class="relative min-h-screen">
        <header class="sticky top-0 z-40 bg-[#FFFBF5]/80 backdrop-blur-xl border-b border-teal-500/10">
            <div class="max-w-4xl mx-auto py-5 px-6 lg:px-12 flex justify-between items-center">
                <div>
                    <a href="{{ URL::signedRoute('portal.dashboard', ['patient' => $budget->patient]) }}"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-primary-600 transition-colors mb-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Volver al portal
                    </a>
                    <h1 class="text-xl font-display font-bold text-stone-800">Detalle del Presupuesto</h1>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border
                    @if($budget->status === 'accepted') bg-emerald-50 text-emerald-700 border-emerald-200
                    @elseif($budget->status === 'sent') bg-amber-50 text-amber-700 border-amber-200
                    @elseif($budget->status === 'rejected') bg-rose-50 text-rose-700 border-rose-200
                    @else bg-stone-100 text-stone-600 border-stone-200 @endif">
                    <span class="w-2 h-2 rounded-full mr-1.5 @if($budget->status === 'sent') bg-amber-500 animate-pulse @else bg-current @endif"></span>
                    @switch($budget->status) @case('accepted') Aceptado @break @case('sent') Enviado @break @case('rejected') Rechazado @break @case('draft') Borrador @break @default {{ $budget->status }} @endswitch
                </span>
            </div>
        </header>

        <main class="max-w-4xl mx-auto py-8 px-6 lg:px-12">
            <!-- Budget Info -->
            <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Información del Presupuesto</h3>
                </div>
                <div class="p-8">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div><dt class="text-xs font-semibold text-stone-400 uppercase tracking-wide">Paciente</dt><dd class="mt-1 text-stone-800 font-semibold text-lg">{{ $budget->patient->name }}</dd></div>
                        <div><dt class="text-xs font-semibold text-stone-400 uppercase tracking-wide">Fecha de Emision</dt><dd class="mt-1 text-stone-700">{{ $budget->created_at->format('d/m/Y') }}</dd></div>
                        <div><dt class="text-xs font-semibold text-stone-400 uppercase tracking-wide">Valido Hasta</dt>
                            <dd class="mt-1 text-stone-700">
                                @if($budget->expires_at) {{ $budget->expires_at->format('d/m/Y') }}
                                    @if($budget->expires_at->isPast() && $budget->status === 'sent') <span class="ml-2 text-rose-600 text-xs font-semibold">(Vencido)</span> @endif
                                @else Sin fecha de vencimiento @endif
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
                    <div class="mt-8 pt-6 border-t border-stone-100">
                        <dt class="text-xs font-semibold text-stone-400 uppercase tracking-wide mb-2">Notas</dt>
                        <dd class="text-stone-700 bg-[#FFFBF5] rounded-2xl p-5 border border-stone-100">{{ $budget->notes }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Budget Items -->
            <div class="bg-white rounded-[2rem] border border-teal-500/10 shadow-lg overflow-hidden mb-8">
                <div class="px-8 py-5 border-b border-stone-100 bg-gradient-to-r from-emerald-50 to-transparent">
                    <h3 class="text-xl font-display font-bold text-stone-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Detalle de Tratamientos
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-[#FFFBF5]/80">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Tratamiento</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-stone-400 uppercase tracking-wider">Cant.</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-stone-400 uppercase tracking-wider">Precio Unit.</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-stone-400 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($budget->items as $item)
                            <tr class="hover:bg-teal-50/30 transition-colors">
                                <td class="px-6 py-4"><div class="text-sm font-semibold text-stone-800">{{ $item->treatment_name }}</div></td>
                                <td class="px-6 py-4 text-sm text-stone-700 text-center">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-sm text-stone-700 text-right">${{ number_format($item->cost, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-stone-800 text-right">${{ number_format($item->cost * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-stone-400">Sin tratamientos en este presupuesto.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-[#FFFBF5]/80">
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
            <div class="bg-white rounded-[2rem] border border-teal-500/10 shadow-lg overflow-hidden">
                <div class="p-6 flex justify-center">
                    <a href="{{ URL::signedRoute('portal.budgets.pdf', ['budget' => $budget]) }}"
                       target="_blank"
                       class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-sm font-semibold text-teal-700 bg-teal-50 border border-teal-200 hover:bg-teal-100 transition-all duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descargar PDF
                    </a>
                </div>
            </div>

            @if($budget->status === 'sent')
            <div class="bg-white rounded-[2rem] border border-teal-500/10 shadow-lg overflow-hidden">
                <div class="px-8 py-5 border-b border-stone-100 bg-gradient-to-r from-amber-50 to-transparent">
                    <h3 class="text-xl font-display font-bold text-stone-800">Que deseas hacer?</h3>
                </div>
                <div class="p-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <form action="{{ URL::signedRoute('portal.budgets.accept', ['budget' => $budget]) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-success-600 to-success-700 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Aceptar Presupuesto
                        </button>
                    </form>
                    <form action="{{ URL::signedRoute('portal.budgets.reject', ['budget' => $budget]) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl text-sm font-semibold text-rose-700 bg-white border border-rose-200 hover:bg-rose-50 transition-all duration-200 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Rechazar Presupuesto
                        </button>
                    </form>
                </div>
            </div>
            @elseif($budget->status === 'accepted')
            <div class="bg-emerald-50 border border-emerald-200 rounded-[2rem] p-10 text-center">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-2xl font-display font-bold text-emerald-800">Presupuesto Aceptado</h3>
                <p class="text-emerald-700 mt-3 max-w-md mx-auto">Tu clinica se pondra en contacto para coordinar el tratamiento.</p>
            </div>
            @elseif($budget->status === 'rejected')
            <div class="bg-rose-50 border border-rose-200 rounded-[2rem] p-10 text-center">
                <div class="w-16 h-16 rounded-2xl bg-rose-100 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h3 class="text-2xl font-display font-bold text-rose-800">Presupuesto Rechazado</h3>
                <p class="text-rose-700 mt-3 max-w-md mx-auto">Contacta a tu clinica si necesitas mas informacion.</p>
            </div>
            @endif
        </main>
    </div>
</body>
</html>
