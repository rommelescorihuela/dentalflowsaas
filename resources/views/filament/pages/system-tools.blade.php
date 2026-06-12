<x-filament-panels::page>
    <div class="space-y-6">
        <div class="px-6 py-8 rounded-xl bg-white border border-gray-200 dark:bg-gray-900 dark:border-white/10 shadow-sm">
            <div class="mx-auto grid max-w-lg justify-items-center text-center">
                <div class="mb-4 rounded-full bg-primary-100 p-3 dark:bg-primary-500/20 text-primary-600">
                    <x-filament::icon
                        icon="heroicon-o-wrench-screwdriver"
                        class="h-6 w-6"
                    />
                </div>
                <h4 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    SaaS — Herramientas del Sistema
                </h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Usa los botones del header para ejecutar comandos. La salida se muestra en la terminal de abajo.
                </p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 dark:border-white/10 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-800 dark:bg-gray-950 border-b border-gray-700">
                <div class="flex gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                </div>
                <span class="text-xs text-gray-400 font-mono ml-2">Terminal — Salida de comandos</span>
                @if($lastOutput)
                <button
                    type="button"
                    class="ml-auto text-xs text-gray-500 hover:text-gray-300 transition-colors font-mono"
                    wire:click="$set('lastOutput', null)"
                >
                    limpiar
                </button>
                @endif
            </div>
            <div
                class="bg-gray-900 dark:bg-gray-950 text-green-400 p-4 font-mono text-sm overflow-auto max-h-[32rem] min-h-[8rem]"
                x-data
                x-ref="terminal"
                x-init="() => { $watch('$wire.lastOutput', () => { $nextTick(() => { $refs.terminal.scrollTop = $refs.terminal.scrollHeight }) }) }"
            >
                @if($lastOutput)
                <pre class="whitespace-pre-wrap leading-relaxed">{{ $lastOutput }}</pre>
                @else
                <div class="text-gray-600 italic">
                    <span class="text-green-500">dentalflow</span>@<span class="text-blue-400">saas</span>:<span class="text-purple-400">~</span>$ _
                </div>
                <p class="text-gray-700 mt-2 text-xs">Ejecuta una acción del header para ver la salida aquí.</p>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
