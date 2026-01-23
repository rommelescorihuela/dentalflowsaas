<div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-50">
    <div
        class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg border border-gray-100">
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Create your Clinic</h2>
            <p class="text-gray-600 text-sm">Start your 14-day free trial</p>
        </div>

        <form wire:submit.prevent="register">
            <!-- Company Name -->
            <div class="mb-4">
                <label for="company_name" class="block font-medium text-sm text-gray-700">Clinic Name</label>
                <input wire:model.blur="company_name" id="company_name" type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                    required autofocus>
                @error('company_name') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Subdomain -->
            <div class="mb-4">
                <label for="subdomain" class="block font-medium text-sm text-gray-700">Clinic URL</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <input wire:model.blur="subdomain" id="subdomain" type="text"
                        class="flex-1 block w-full min-w-0 rounded-none rounded-l-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border border-r-0"
                        placeholder="myclinic" required>
                    <span
                        class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                        .dentalflow.com
                    </span>
                </div>
                @error('subdomain') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <hr class="my-6 border-gray-100">

            <!-- Admin Name -->
            <div class="mb-4">
                <label for="name" class="block font-medium text-sm text-gray-700">Full Name</label>
                <input wire:model="name" id="name" type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                    required>
                @error('name') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                <input wire:model="email" id="email" type="email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                    required>
                @error('email') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                <input wire:model="password" id="password" type="password"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                    required autocomplete="new-password">
                @error('password') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirm
                    Password</label>
                <input wire:model="password_confirmation" id="password_confirmation" type="password"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                    required>
            </div>

            @if ($errors->has('base'))
                <div class="mb-4 text-red-600 text-sm text-center">
                    {{ $errors->first('base') }}
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    Already registered?
                </a>

                <button type="submit"
                    class="ml-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 transform hover:scale-105 shadow-md">
                    <span wire:loading.remove wire:target="register">Register Clinic</span>
                    <span wire:loading wire:target="register">Processing...</span>
                </button>
            </div>
        </form>
    </div>
</div>