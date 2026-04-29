<x-filament-panels::page>
    {{ $this->form }}

    @if($this->budget)
    <div class="mt-6">
        <x-filament::section>
            <x-slot name="heading">
                Presupuesto Generado
            </x-slot>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        @php
                            $statusLabels = [
                                'draft' => 'Borrador',
                                'sent' => 'Enviado',
                                'accepted' => 'Aceptado',
                                'rejected' => 'Rechazado',
                            ];
                        @endphp
                        {{ $statusLabels[$this->budget->status] ?? ucfirst($this->budget->status) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        ${{ number_format($this->budget->total, 0, ',', '.') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Items</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $this->budget->items()->count() }}
                    </dd>
                </div>
            </div>

            @if($this->budget->items->isNotEmpty())
            <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 text-gray-500 dark:text-gray-400">Tratamiento</th>
                            <th class="text-right py-2 text-gray-500 dark:text-gray-400">Cant.</th>
                            <th class="text-right py-2 text-gray-500 dark:text-gray-400">Costo</th>
                            <th class="text-right py-2 text-gray-500 dark:text-gray-400">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->budget->items as $item)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 text-gray-900 dark:text-white">{{ $item->treatment_name }}</td>
                            <td class="py-2 text-right text-gray-600 dark:text-gray-300">{{ $item->quantity }}</td>
                            <td class="py-2 text-right text-gray-600 dark:text-gray-300">${{ number_format($item->cost, 0, ',', '.') }}</td>
                            <td class="py-2 text-right text-gray-900 dark:text-white font-medium">${{ number_format($item->cost * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </x-filament::section>
    </div>
    @endif
</x-filament-panels::page>
