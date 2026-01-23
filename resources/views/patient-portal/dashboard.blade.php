<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Portal - {{ $patient->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    Hello, {{ $patient->name }}
                </h1>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    Dental Portal
                </span>
            </div>
        </header>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-end">
                <a href="{{ route('portal.book', $patient) }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <!-- Heroicon name: solid/calendar -->
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                    </svg>
                    Reservar Cita
                </a>
            </div>
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Appointments Section -->
                <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Your Appointments
                        </h3>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse($patient->appointments as $appointment)
                                            <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="text-sm font-medium text-indigo-600 truncate">
                                                            {{ ucfirst($appointment->type) }}
                                                        </p>
                                                        <div class="flex items-center">
                                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            <p class="text-sm text-gray-500">
                                                                {{ $appointment->start_time->format('F j, Y, g:i a') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="ml-2 flex-shrink-0 flex">
                                                        <p
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                                    {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' :
                            ($appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                            {{ ucfirst($appointment->status) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                        @empty
                            <li class="px-4 py-4 sm:px-6 text-gray-500 text-center">No appointments found.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Budgets Section -->
                <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Your Budgets
                        </h3>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse($patient->budgets as $budget)
                                            <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">
                                                            Total: ${{ number_format($budget->total, 2) }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            Expires: {{ $budget->expires_at?->format('Y-m-d') ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                                    {{ $budget->status === 'accepted' ? 'bg-green-100 text-green-800' :
                            ($budget->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                            {{ ucfirst($budget->status) }}
                                                        </span>
                                                        @if($budget->status === 'sent')
                                                            <form action="{{ route('portal.budgets.accept', $budget) }}" method="POST">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Accept</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                        @empty
                            <li class="px-4 py-4 sm:px-6 text-gray-500 text-center">No budgets found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>

</html>