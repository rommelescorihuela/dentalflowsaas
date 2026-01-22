<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <div
            class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg border border-gray-100 text-center">

            <div class="mb-6 text-green-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-16 h-16 mx-auto">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-4">Clinic Created Successfully!</h2>

            <p class="text-gray-600 mb-8">
                Your dental clinic <strong>{{ $clinic->name }}</strong> is ready.
            </p>

            <div class="bg-blue-50 p-4 rounded-lg mb-8 text-left">
                <p class="text-sm text-blue-800 font-semibold mb-1">Access URL:</p>
                <a href="{{ $url }}" class="text-blue-600 underline break-all font-mono">{{ $url }}</a>

                <p class="text-sm text-blue-800 font-semibold mb-1 mt-4">Username:</p>
                <span class="font-mono text-gray-700">See your email</span>
            </div>

            <a href="{{ $url }}"
                class="w-full inline-block px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                Go to My Clinic
            </a>
        </div>
    </div>
</x-guest-layout>