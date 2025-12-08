<x-app-layout>
    {{--  IMPORTANTE: Asegúrate de que este meta tag esté en tu archivo app.blade.php (el layout principal) --}}
    {{-- <head>
        ...
        <meta name="csrf-token" content="{{ csrf_token() }}">
        ...
    </head> --}}

    <div class="max-w-6xl mx-auto py-8">
        
        {{-- Muestra los mensajes de sesión (ej: éxito o error al guardar) --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        

            {{-- Sección de Búsqueda y Agregar Ciudad (IZQUIERDA) --}}
            <div class="bg-white p-6 rounded-xl shadow-lg h-full">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Buscar y Agregar Ciudad</h2>
                
                {{-- Formulario básico (si se usa sin AJAX) --}}
                <form method="POST" action="{{ route('cities.store') }}" class="flex space-x-3 mb-4">
                    @csrf
                    <input type="text" name="name" class="w-full border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500" placeholder="Escribe aquí y presiona 'Guardar'" required>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Guardar
                    </button>
                </form>


    
            </div>


        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Ciudades guardadas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($data as $city)
                    <div class="bg-white rounded-xl shadow-md p-5 border hover:shadow-xl transition flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $city['name'] }} ({{ $city['country'] ?? '' }})</h3>
                            <p class="text-3xl font-bold mt-2 text-blue-600">{{ $city['temp'] }} °C</p>
                            <p class="text-gray-600 capitalize">{{ $city['desc'] }}</p>
                        </div>
                        <div class="flex justify-between mt-4 border-t pt-3">
                            {{-- Cambiado el enlace para que apunte al 'dashboard' y muestre el clima de esa ciudad --}}
                            <a href="{{ route('dashboard', ['city' => $city['name']]) }}" class="text-blue-600 font-semibold hover:text-blue-800 transition">
                                Ver detalles
                            </a>
                            <form action="{{ route('cities.destroy', $city['id']) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta ciudad?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-semibold hover:text-red-800 transition">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 col-span-3">Aún no tienes ciudades guardadas.</p>
                @endforelse
            </div>
        </div>
        
    </div>

    {{-- Scripts de JavaScript --}}
    <script>
        // Variables globales para las rutas, usando el helper route() de Laravel
        const SEARCH_URL = "{{ route('search.cities') }}"; // Ruta para buscar ciudades
        // ✅ CORRECCIÓN: Usar la ruta con nombre 'cities.save'
        const SAVE_URL = "{{ route('cities.save') }}";     // Ruta para guardar ciudad (POST)
        
        
        /**
         * Función para guardar la ciudad seleccionada mediante AJAX
         * @param {string} name - Nombre de la ciudad
         * @param {string} country - Código del país (opcional)
         */
        function saveCity(name, country) {
            
            fetch(SAVE_URL, { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Obtener el token del meta tag CSRF (asegúrate de que está en app.blade.php)
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                },
                body: JSON.stringify({ 
                    name: name, 
                    country: country,
                    // Asegúrate de enviar los datos que tu controlador CityController::store espera
                })
            })
            .then(res => {
                // Manejo de errores HTTP (incluyendo 404/not found que causa el error JSON)
                if (!res.ok) {
                    // Intenta leer el mensaje de error del JSON si existe, si no, usa el status
                    return res.json().then(data => { 
                        throw new Error(data.message || `Error del servidor: ${res.status}`); 
                    }).catch(() => {
                        // Si no es un JSON válido (ej. error 404 HTML), lanza un error genérico
                        throw new Error(`Error en la solicitud: ${res.statusText} (${res.status}). Verifica la consola.`);
                    });
                }
                return res.json();
            })
            .then(data => {
                alert(data.message || 'Ciudad guardada con éxito.');
                // Recargar la página para ver la ciudad en la lista y limpiar los resultados
                location.reload(); 
            })
            .catch(error => {
                console.error('Error al guardar la ciudad:', error);
                alert(' Error al guardar la ciudad: ' + error.message);
            });
        }


        // Script para la búsqueda en tiempo real
        document.getElementById('searchCity').addEventListener('keyup', function () {
            const query = this.value.trim();
            const resultsList = document.getElementById('results');

            if (query.length < 2) {
                resultsList.innerHTML = "";
                return;
            }

            fetch(`${SEARCH_URL}?q=${query}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error en la búsqueda: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    let resultsHtml = "";
                    if (data.length === 0) {
                        resultsHtml = '<li class="p-3 text-gray-500">No se encontraron resultados.</li>';
                    } else {
                        data.forEach(item => {
                            // Escapa comillas simples para evitar romper la llamada JS en onclick
                            const name = item.name.replace(/'/g, "\\'");
                            const country = item.country ? item.country.replace(/'/g, "\\'") : '';
                            
                            resultsHtml += `<li class="p-3 border-b cursor-pointer hover:bg-gray-100 text-gray-800"
                                onclick="saveCity('${name}', '${country}')">
                                 ${item.name} <span class="text-gray-500">(${item.country ?? ''})</span>
                            </li>`;
                        });
                    }
                    resultsList.innerHTML = resultsHtml;
                })
                .catch(error => {
                    console.error('Error al buscar ciudades:', error);
                    resultsList.innerHTML = '<li class="p-3 text-red-500">Error al contactar al servidor de búsqueda.</li>';
                });
        });
    </script>
</x-app-layout>