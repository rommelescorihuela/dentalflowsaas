<x-filament-panels::page>
    <div class="mb-6 rounded-xl bg-gradient-to-r from-cyan-50 to-teal-50 dark:from-cyan-900/20 dark:to-teal-900/20 border border-cyan-200 dark:border-cyan-800 p-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">¡Bienvenido a DentalFlow!</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Completa estos 4 pasos para configurar tu clínica. Te tomará menos de 5 minutos.
                Puedes editar todo esto más adelante desde "Configuración".
            </p>
        </div>
    </div>

    <form wire:submit="submit" id="onboarding-form">
        {{ $this->form }}
    </form>
</x-filament-panels::page>