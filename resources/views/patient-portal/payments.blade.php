@extends('patient-portal.layout')

@section('title', 'Pagos')

@section('content')
<h2 class="text-2xl font-display font-bold text-stone-800 mb-6 portal-reveal">Mis Pagos</h2>

<!-- Payment Summary -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8 portal-reveal">
    <div class="bg-white rounded-3xl border border-teal-500/10 shadow-lg p-6">
        <p class="text-sm text-stone-500">Total Pagado</p>
        <p class="text-3xl font-display font-bold text-emerald-700 mt-1">${{ number_format($totalPaid, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-3xl border border-teal-500/10 shadow-lg p-6">
        <p class="text-sm text-stone-500">Total Presupuestado</p>
        <p class="text-3xl font-display font-bold text-primary-600 mt-1">${{ number_format($totalBudgeted, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-3xl border border-teal-500/10 shadow-lg p-6">
        <p class="text-sm text-stone-500">Pendiente</p>
        <p class="text-3xl font-display font-bold text-amber-600 mt-1">${{ number_format(max(0, $totalBudgeted - $totalPaid), 0, ',', '.') }}</p>
    </div>
</div>

<!-- Payments List -->
<div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-100 mb-8 portal-reveal portal-reveal-delay-1">
    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-transparent">
        <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <span class="bg-emerald-600 text-white p-2.5 rounded-xl mr-3 shadow-md shadow-emerald-600/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            Historial de Pagos
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-[#FFFBF5]/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Monto</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Método</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Referencia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($payments as $payment)
                <tr class="hover:bg-emerald-50/30 transition-colors">
                    <td class="px-6 py-4 text-sm text-stone-700">{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : $payment->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-stone-800">${{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-stone-700">
                        @switch($payment->payment_method)
                            @case('cash') Efectivo @break
                            @case('transfer') Transferencia @break
                            @case('card') Tarjeta @break
                            @case('pix') PIX @break
                            @default {{ $payment->payment_method ?? '—' }}
                        @endswitch
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium
                            @if($payment->status === 'completed') bg-emerald-100 text-emerald-700
                            @elseif($payment->status === 'pending') bg-amber-100 text-amber-700
                            @else bg-red-100 text-red-700 @endif">
                            @switch($payment->status)
                                @case('completed') Completado @break
                                @case('pending') Pendiente @break
                                @case('rejected') Rechazado @break
                                @default {{ ucfirst($payment->status) }}
                            @endswitch
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-stone-500">{{ $payment->reference ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-stone-400">
                        <svg class="mx-auto h-12 w-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-display font-semibold text-lg">Sin pagos registrados</p>
                        <p class="text-sm mt-1">Tus pagos aparecerán aquí cuando realices alguno.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
