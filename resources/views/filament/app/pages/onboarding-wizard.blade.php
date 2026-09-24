<x-filament-panels::page>
    <div class="mb-6 rounded-xl bg-gradient-to-r from-primary-50 to-secondary-50 dark:from-primary-900/20 dark:to-secondary-900/20 border border-primary-200 dark:border-primary-800 p-6">
        <div>
            <h2 class="text-lg font-semibold text-sand-900 dark:text-sand-100">Bienvenido a DentalFlow</h2>
            <p class="text-sm text-sand-600 dark:text-sand-400 mt-1">
                Completa estos 4 pasos para configurar tu clínica. Te tomará menos de 5 minutos.
                Puedes editar todo esto más adelante desde "Configuración".
            </p>
        </div>
    </div>

    <form wire:submit="submit" id="onboarding-form">
        {{ $this->form }}
    </form>
</x-filament-panels::page>