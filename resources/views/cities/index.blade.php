<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Városok Keresése</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <!-- FEJLÉC -->
    <nav class="bg-white shadow mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center font-bold text-indigo-600 text-xl">
                        Magyar Városok
                    </div>
                </div>
                <div class="flex items-center">
                    @if (Route::has('login'))
                        <div class="space-x-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-indigo-600">Fiók</a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">Belépés</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-gray-700 hover:text-indigo-600">Regisztráció</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- FŐ TARTALOM -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight mb-6">
            Városok Keresése
        </h2>

        <div class="space-y-6">
            
            <!-- 1. DOBOZ: SZŰRŐK -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('cities.index') }}" id="filterForm">
                    <label for="county" class="block text-sm font-medium text-gray-700 mb-2">Válasszon megyét:</label>
                    <select name="county_id" id="county" onchange="this.form.submit()" 
                            class="block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border">
                        <option value="">-- Válasszon --</option>
                        @foreach($counties as $county)
                            <option value="{{ $county['id'] }}" {{ $selectedCountyId == $county['id'] ? 'selected' : '' }}>
                                {{ $county['name'] }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- BETŰVÁLASZTÓ -->
                @if(!empty($letters))
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Szűrés kezdőbetű szerint:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($letters as $letter)
                                <a href="{{ route('cities.index', ['county_id' => $selectedCountyId, 'letter' => $letter]) }}"
                                   class="px-4 py-2 border rounded-md text-sm font-semibold transition-colors
                                          {{ $selectedLetter == $letter 
                                             ? 'bg-indigo-600 text-white border-indigo-600' 
                                             : 'bg-gray-50 text-gray-700 border-gray-300 hover:bg-gray-100' }}">
                                    {{ $letter }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- 2. DOBOZ: EREDMÉNYEK LISTÁJA -->
            @if(!empty($cities))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Találatok: {{ count($cities) }} db</h3>
                        
                        <div class="flex gap-2">
                            <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition">CSV Export</button>
                            <button class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition">PDF Export</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Település</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Irányítószám</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Megye</th>
                                    @auth
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Műveletek</th>
                                    @endauth
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cities as $zipItem)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <!-- Település neve -->
                                            {{ $zipItem['settlement']['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <!-- ITT VOLT A HIBA: 'zip_code' helyett 'code' kell -->
                                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                                {{ $zipItem['code'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <!-- Megye neve -->
                                            {{ $zipItem['settlement']['county']['name'] }}
                                        </td>
                                        @auth
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Szerk.</a>
                                                <button class="text-red-600 hover:text-red-900">Törlés</button>
                                            </td>
                                        @endauth
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($selectedLetter)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Nincs találat a kiválasztott feltételekre.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</body>
</html>
