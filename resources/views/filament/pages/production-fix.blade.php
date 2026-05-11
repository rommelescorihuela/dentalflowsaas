<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Información --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-information-circle class="w-5 h-5" />
                    Herramientas de Producción
                </div>
            </x-slot>
            
            <div class="prose dark:prose-invert">
                <p>Usa estas herramientas para solucionar problemas comunes en producción:</p>
                <ul>
                    <li><strong>Fix Dominios:</strong> Registra el dominio de la clínica en la base de datos</li>
                    <li><strong>Ejecutar Migraciones:</strong> Ejecuta migraciones pendientes en central y tenants</li>
                    <li><strong>Verificar Inventories:</strong> Revisa la estructura de la tabla inventories</li>
                </ul>
            </div>
        </x-filament::section>

        {{-- Output --}}
        @if($lastOutput)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-terminal class="w-5 h-5" />
                    Resultado
                </div>
            </x-slot>
            
            <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-auto max-h-96">
                <pre class="whitespace-pre-wrap">{{ $lastOutput }}</pre>
            </div>
            
            @if($fixApplied)
            <div class="mt-4 p-4 bg-success-500/10 border border-success-500 rounded-lg">
                <div class="flex items-center gap-2 text-success-600 dark:text-success-400">
                    <x-heroicon-o-check-circle class="w-5 h-5" />
                    <span>✅ Fix aplicado exitosamente. Ya puedes acceder a la clínica.</span>
                </div>
            </div>
            @endif
        </x-filament::section>
        @endif

        {{-- Enlaces útiles --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-link class="w-5 h-5" />
                    Enlaces Útiles
                </div>
            </x-slot>
            
            <div class="flex flex-wrap gap-3">
                <a href="{{ url('/clinicatest/app/login') }}" class="inline-flex">
                    <x-filament::button color="primary" icon="heroicon-o-lock-closed">
                        Ir a Login Clínica
                    </x-filament::button>
                </a>
                
                <a href="{{ url('/admin') }}" class="inline-flex">
                    <x-filament::button color="gray" icon="heroicon-o-shield-check">
                        Admin Panel
                    </x-filament::button>
                </a>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
