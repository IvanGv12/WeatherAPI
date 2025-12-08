@if(!isset($city))
<script>
document.addEventListener("DOMContentLoaded", () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            window.location.href = `/auto-weather?lat=${lat}&lon=${lon}`;
        }, () => {
            console.log("No se pudo obtener la ubicación del usuario.");
        });
    }
});
</script>
@endif

<script>
document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("refreshLocation");
    if (!btn) return;

    btn.addEventListener("click", () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                window.location.href = `/auto-weather?lat=${lat}&lon=${lon}`;
            }, () => {
                alert("No fue posible obtener la ubicación. Activa el acceso a ubicación en tu navegador.");
            });
        } else {
            alert("Tu navegador no soporta geolocalización.");
        }
    });
});
</script>

<x-app-layout>
    <div class="max-w-6xl mx-auto py-8">

        {{-- Buscador manual --}}
        <form method="GET" action="{{ route('dashboard') }}" class="relative mb-4">
            <div class="flex">
                <input 
                    type="text" 
                    name="city" 
                    value="{{ $city ?? '' }}"
                    class="w-full border-gray-300 rounded-lg p-3"
                    placeholder="Buscar por ciudad o código postal..."
                    id="citySearch"
                    autocomplete="off"
                >
                <button class="ml-2 bg-blue-600 text-white px-4 rounded-lg hover:bg-blue-700">
                    Buscar
                </button>
            </div>

            <!--  Contenedor de sugerencias de Algolia -->
            <div id="suggestions" class="absolute bg-white w-full shadow-lg rounded-b-lg z-10"></div>
        </form>

        {{-- Botón actualizar ubicación --}}
        <div class="mb-6">
            <button 
                id="refreshLocation" 
                class="bg-gray-700 text-blue px-4 py-2 rounded-lg hover:bg-gray-800"
                type="button"
            >
                Actualizar ubicación
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Clima actual --}}
            <div class="p-6 shadow-lg rounded-xl bg-white">
                <h2 class="text-xl font-bold mb-4">Clima actual en {{ $city ?? '---' }}</h2>

                @if(isset($current['main']))
                    <p>Temperatura: <strong>{{ $current['main']['temp'] }} °C</strong></p>
                    <p>Sensación térmica: {{ $current['main']['feels_like'] }} °C</p>
                    <p>Humedad: {{ $current['main']['humidity'] }}%</p>
                    <p>Velocidad del viento: {{ $current['wind']['speed'] }} m/s</p>
                @elseif(isset($current['message']))
                    <p class="text-red-600 font-bold">{{ ucfirst($current['message']) }}</p>
                @else
                    <p class="text-red-600 font-bold">Error al obtener el clima actual.</p>
                @endif
            </div>

            {{-- Pronóstico --}}
            <div class="p-6 shadow-lg rounded-xl bg-white">
                <h2 class="text-xl font-bold mb-4">Pronóstico extendido</h2>

                @if(isset($forecast['list']))
                    <ul>
                        @foreach ($forecast['list'] as $day)
                            <li class="border-b py-2">
                                {{ $day['dt_txt'] }} |
                                {{ $day['main']['temp'] }} °C |
                                {{ ucfirst($day['weather'][0]['description']) }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-red-600 font-bold">No fue posible cargar el pronóstico.</p>
                @endif
            </div>

        </div>
    </div>

    {{--  Algolia Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/algoliasearch@4.14.2/dist/algoliasearch-lite.umd.js"></script>
    <script>
        const client = algoliasearch("0DBSSOT5JD", "84a1838c83902737f27e827cc0afcf37");
        const index = client.initIndex('cities');

        const input = document.getElementById("citySearch");

        input.addEventListener("input", async () => {
            if (input.value.length < 2) {
                document.getElementById("suggestions").innerHTML = "";
                return;
            }

            const result = await index.search(input.value);
            let list = "";

            result.hits.forEach(hit => {
                list += `<div class="px-3 py-2 hover:bg-gray-200 cursor-pointer suggestion" data-name="${hit.name}">
                            ${hit.name} - <span class="text-gray-600">${hit.country}</span>
                         </div>`;
            });

            document.getElementById("suggestions").innerHTML = list;
        });

        document.addEventListener("click", (e) => {
            if (e.target.classList.contains("suggestion")) {
                input.value = e.target.dataset.name;
                document.getElementById("suggestions").innerHTML = "";
            }
        });
    </script>
</x-app-layout>
