<div class="ld-card p-6 sm:p-8">
    <h1 class="ld-display text-[1.75rem]">Crea tu clínica</h1>
    <p class="mt-2 text-[0.9375rem] text-sand-700">14 días de prueba. El registro no pide tarjeta.</p>

    <form wire:submit.prevent="register" class="mt-6 grid gap-4">
        <div>
            <label for="company_name" class="ld-label">Nombre de la clínica</label>
            <input wire:model.blur="company_name" id="company_name" type="text" required autofocus
                   autocomplete="organization" class="ld-field mt-1.5"
                   @error('company_name') aria-invalid="true" aria-describedby="company_name-error" @enderror>
            @error('company_name') <p id="company_name-error" role="alert" class="ld-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="subdomain" class="ld-label">Dirección de la clínica</label>
            <div class="mt-1.5 flex">
                <input wire:model.blur="subdomain" id="subdomain" type="text" required
                       placeholder="mi-clinica" inputmode="url" autocapitalize="none" spellcheck="false"
                       class="ld-field !rounded-r-none"
                       @error('subdomain') aria-invalid="true" aria-describedby="subdomain-error" @enderror>
                <span class="inline-flex items-center border border-l-0 border-sand-300 bg-sand-50 px-3 text-[0.875rem] text-sand-600">.dentalflow.com</span>
            </div>
            <p class="mt-1 text-[0.75rem] text-sand-600">Se usa para entrar al panel y para la página pública de tu clínica.</p>
            @error('subdomain') <p id="subdomain-error" role="alert" class="ld-error">{{ $message }}</p> @enderror
        </div>

        <hr class="border-sand-200">

        <div>
            <label for="name" class="ld-label">Tu nombre</label>
            <input wire:model="name" id="name" type="text" required autocomplete="name" class="ld-field mt-1.5"
                   @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
            @error('name') <p id="name-error" role="alert" class="ld-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="ld-label">Correo</label>
            <input wire:model="email" id="email" type="email" required autocomplete="email" class="ld-field mt-1.5"
                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email') <p id="email-error" role="alert" class="ld-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="ld-label">Contraseña</label>
            <input wire:model="password" id="password" type="password" required autocomplete="new-password" class="ld-field mt-1.5"
                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            @error('password') <p id="password-error" role="alert" class="ld-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="ld-label">Repite la contraseña</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" required autocomplete="new-password" class="ld-field mt-1.5">
        </div>

        @if ($errors->has('base'))
            <p role="alert" class="ld-error">{{ $errors->first('base') }}</p>
        @endif

        <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
            <a class="text-[0.875rem] text-sand-600 underline underline-offset-4 hover:text-sand-900" href="{{ route('login') }}">
                ¿Ya tienes cuenta? Entrar
            </a>

            <button type="submit"
                    class="ld-btn ld-btn-primary"
                    wire:loading.attr="disabled" wire:target="register">
                <span wire:loading.remove wire:target="register">Crear mi clínica</span>
                <span wire:loading wire:target="register">Creando…</span>
            </button>
        </div>
    </form>
</div>
