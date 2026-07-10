@extends('patient-portal.layout')

@section('title', 'Recetas')

@section('content')
<h2 class="text-2xl font-display font-bold text-stone-800 mb-6 portal-reveal">Mis Recetas</h2>

@forelse($prescriptions as $prescription)
<div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-100 mb-6 portal-reveal portal-reveal-delay-1">
    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-2.5 rounded-xl mr-3 shadow-lg shadow-primary-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
            Receta #{{ $prescription->id }}
        </h3>
        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
            @if($prescription->status === 'active') bg-green-100 text-green-700
            @elseif($prescription->status === 'completed') bg-gray-100 text-gray-700
            @else bg-red-100 text-red-700 @endif">
            @switch($prescription->status)
                @case('active') Activa @break
                @case('completed') Completada @break
                @case('cancelled') Cancelada @break
                @default {{ ucfirst($prescription->status) }}
            @endswitch
        </span>
    </div>
    <div class="p-6">
        @if($prescription->diagnosis)
        <div class="mb-4 p-4 bg-stone-50 rounded-xl">
            <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Diagnóstico</p>
            <p class="text-stone-700">{{ $prescription->diagnosis }}</p>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-[#FFFBF5]/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Medicamento</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Dosis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Frecuencia</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Duración</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Cantidad</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-stone-400 uppercase tracking-wider">Indicaciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($prescription->items as $item)
                    <tr class="hover:bg-teal-50/30 transition-colors">
                        <td class="px-4 py-3 text-sm font-semibold text-stone-800">{{ $item->medication }}</td>
                        <td class="px-4 py-3 text-sm text-stone-700">{{ $item->dosage ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-stone-700">{{ $item->frequency ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-stone-700">{{ $item->duration ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-stone-700">{{ $item->quantity ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-stone-600 italic">{{ $item->indications ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($prescription->doctor)
        <div class="mt-4 pt-4 border-t border-stone-100 flex justify-between items-center">
            <div>
                <p class="text-xs text-stone-500">Recetado por</p>
                <p class="text-sm font-semibold text-stone-700">{{ $prescription->doctor->name }}</p>
            </div>
            @if($prescription->signed_at)
            <div class="text-right">
                <p class="text-xs text-stone-500">Firmado el</p>
                <p class="text-sm text-stone-700">{{ $prescription->signed_at->format('d/m/Y H:i') }}</p>
            </div>
            @endif
        </div>
        @endif

        @if($prescription->notes)
        <div class="mt-4 pt-4 border-t border-stone-100">
            <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Notas</p>
            <p class="text-sm text-stone-600">{{ $prescription->notes }}</p>
        </div>
        @endif
    </div>
</div>
@empty
<div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-12 text-center">
    <svg class="mx-auto h-16 w-16 mb-4 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <p class="font-display font-semibold text-lg text-stone-500">Sin recetas activas</p>
    <p class="text-sm text-stone-400 mt-1">Las recetas que te recete tu doctor aparecerán aquí.</p>
</div>
@endforelse

@if($prescriptions->hasPages())
<div class="mt-6">
    {{ $prescriptions->links() }}
</div>
@endif
@endsection
