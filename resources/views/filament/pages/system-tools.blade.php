<x-filament-panels::page>
    <div class="space-y-6">
        <div class="px-6 py-12 rounded-xl bg-white border border-gray-200 dark:bg-gray-900 dark:border-white/10 shadow-sm">
            <div class="mx-auto grid max-w-lg justify-items-center text-center">
                <div class="mb-4 rounded-full bg-warning-100 p-3 dark:bg-warning-500/20 text-warning-600">
                    <x-filament::icon
                        icon="heroicon-o-wrench-screwdriver"
                        class="h-6 w-6"
                    />
                </div>
                <h4 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    SaaS System Tools
                </h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Use the actions in the top-right header to execute global seeders or perform system maintenance tasks directly from the dashboard.
                </p>
            </div>
        </div>

        @if($lastOutput)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-code-bracket class="w-5 h-5" />
                    Resultado
                </div>
            </x-slot>
            
            <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-auto max-h-96">
                <pre class="whitespace-pre-wrap">{{ $lastOutput }}</pre>
            </div>
        </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
