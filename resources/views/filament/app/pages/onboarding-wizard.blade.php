<x-filament-panels::page>
    <div class="mb-6 rounded-xl bg-gradient-to-r from-cyan-50 to-teal-50 dark:from-cyan-900/20 dark:to-teal-900/20 border border-cyan-200 dark:border-cyan-800 p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-cyan-500 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">¡Bienvenido a DentalFlow!</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Completa estos 4 pasos para configurar tu clínica. Te tomará menos de 5 minutos.
                    Puedes editar todo esto más adelante desde "Configuración".
                </p>
            </div>
        </div>
    </div>

    <form wire:submit="submit">
        {{ $this->form }}

        <div class="pt-6">
            <x-filament::button type="submit" size="lg" color="primary">
                Finalizar configuración
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
