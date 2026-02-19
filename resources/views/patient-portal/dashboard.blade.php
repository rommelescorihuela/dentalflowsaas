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
                <a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}"
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
                <!-- Próximas Citas Section (Stitch Component Integration) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                    <div class="px-6 py-5 border-b border-gray-100 bg-white">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </span>
                                Próximas Citas
                            </h3>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50">
                        <div class="space-y-4">
                            @forelse($patient->appointments as $appointment)
                            <div
                                class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center group">
                                <div class="flex items-start space-x-4">
                                    <div
                                        class="flex-shrink-0 bg-blue-50 text-blue-500 rounded-xl p-3 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200">
                                        <!-- Dental Tooth Icon -->
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4
                                            class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                            {{ ucfirst($appointment->type) }}
                                        </h4>

                                        <div class="flex items-center space-x-4 mt-2">
                                            <div class="flex items-center text-sm text-gray-500">
                                                <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $appointment->start_time->format('d M, Y') }}
                                            </div>
                                            <div class="flex items-center text-sm text-gray-500">
                                                <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $appointment->start_time->format('h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0 flex items-center">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-700 border border-green-200' : 
                                            ($appointment->status === 'cancelled' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200') }}">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5 
                                                {{ $appointment->status === 'confirmed' ? 'bg-green-500' : 
                                                ($appointment->status === 'cancelled' ? 'bg-red-500' : 'bg-yellow-500') }}">
                                        </span>
                                        {{ ucfirst($appointment->status === 'confirmed' ? 'Confirmada' :
                                        ($appointment->status === 'cancelled' ? 'Cancelada' : 'Pendiente')) }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-4">
                                    <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">No tienes citas próximas</h3>
                                <p class="mt-1 text-sm text-gray-500">Programa tu próxima visita ahora.</p>
                                <div class="mt-6">
                                    <a href="{{ URL::signedRoute('portal.book', ['patient' => $patient]) }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Reservar Cita
                                    </a>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
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
                                    <form
                                        action="{{ URL::signedRoute('portal.budgets.accept', ['budget' => $budget]) }}"
                                        method="POST">
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