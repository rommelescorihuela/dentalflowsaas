<x-filament-panels::page>
    @php
        $clinic = $this->resolveTenant();
        $subscription = $clinic?->subscription;
        $status = $this->subscriptionStatus;
        $planLabel = $this->planLabel;
    @endphp

    <div class="space-y-6">
        @if ($subscription)
            <x-filament::section>
                <x-slot name="heading">Estado de la suscripción</x-slot>
                <x-slot name="description">Resumen de tu plan actual</x-slot>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Plan</p>
                        <p class="text-lg font-semibold">{{ $planLabel }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                        <p class="text-lg font-semibold">
                            <x-filament::badge color="{{ $status?->color() ?? 'gray' }}">
                                {{ $status?->label() ?? 'Sin suscripción' }}
                            </x-filament::badge>
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @if ($status === \App\Enums\SubscriptionStatus::Trialing)
                                Trial termina
                            @else
                                Próxima renovación
                            @endif
                        </p>
                        <p class="text-lg font-semibold">
                            @if ($status === \App\Enums\SubscriptionStatus::Trialing)
                                {{ $subscription->trial_ends_at?->format('d/m/Y') ?? '—' }}
                            @else
                                {{ $subscription->current_period_end?->format('d/m/Y') ?? '—' }}
                            @endif
                        </p>
                    </div>
                </div>
            </x-filament::section>
        @endif

        @if ($status === \App\Enums\SubscriptionStatus::Suspended || $status === \App\Enums\SubscriptionStatus::PastDue)
            <x-filament::section>
                <x-slot name="heading">⚠️ Acceso restringido</x-slot>
                <x-slot name="description">
                    @if ($status === \App\Enums\SubscriptionStatus::Suspended)
                        Tu suscripción está suspendida. Sube tu comprobante de pago para reactivar el acceso.
                    @else
                        Tu período de gracia termina pronto. Sube tu comprobante para evitar la suspensión.
                    @endif
                </x-slot>
            </x-filament::section>
        @endif

        <form wire:submit="submitPayment">
            {{ $this->paymentForm }}

            <div class="pt-6">
                <x-filament::button type="submit" size="md">
                    Enviar comprobante
                </x-filament::button>
            </div>
        </form>

        @if ($subscription)
            <x-filament::section>
                <x-slot name="heading">Historial de pagos</x-slot>
                <x-slot name="description">Comprobantes enviados y su estado</x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                                <th class="pb-2 pr-4">Fecha</th>
                                <th class="pb-2 pr-4">Método</th>
                                <th class="pb-2 pr-4">Monto</th>
                                <th class="pb-2 pr-4">Referencia</th>
                                <th class="pb-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscription->payments()->latest()->limit(10)->get() as $payment)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="py-2 pr-4">{{ $payment->paid_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="py-2 pr-4">{{ $payment->method }}</td>
                                    <td class="py-2 pr-4">{{ $payment->amount }} {{ $payment->currency }}</td>
                                    <td class="py-2 pr-4">{{ $payment->reference ?? '—' }}</td>
                                    <td class="py-2">
                                        @php
                                            $colors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                                            $labels = ['pending' => 'En revisión', 'approved' => 'Aprobado', 'rejected' => 'Rechazado'];
                                        @endphp
                                        <x-filament::badge color="{{ $colors[$payment->status] ?? 'gray' }}">
                                            {{ $labels[$payment->status] ?? $payment->status }}
                                        </x-filament::badge>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($subscription->payments()->count() === 0)
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-400">No hay pagos registrados</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
