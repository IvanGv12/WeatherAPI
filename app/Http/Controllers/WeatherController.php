<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    // Vista principal con búsqueda manual
    public function index(Request $request)
    {
        $city = $request->get('city');

        // Si aún no se busca nada, se manda vista sin datos
        if (!$city) {
            return view('dashboard.index', [
                'current' => [],
                'forecast' => [],
                'city' => null
            ]);
        }

        $key = config('services.weather.key');

        // Consulta de clima actual
        $current = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'q' => $city,
            'appid' => $key,
            'units' => 'metric',
            'lang' => 'es'
        ])->json();

        // Si no existe "main", la ciudad fue incorrecta
        if (!isset($current['main'])) {
            return view('dashboard.index', [
                'current' => ['message' => 'Ciudad no encontrada.'],
                'forecast' => [],
                'city' => $city
            ]);
        }

        // Consulta de pronóstico
        $forecast = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
            'q' => $city,
            'appid' => $key,
            'units' => 'metric',
            'lang' => 'es'
        ])->json();

        return view('dashboard.index', [
            'current' => $current,
            'forecast' => $forecast,
            'city' => $current['name']
        ]);
    }

    // Vista automática por geolocalización
    public function autoWeather(Request $request)
    {
        $lat = $request->lat;
        $lon = $request->lon;

        if (!$lat || !$lon) {
            return redirect()->route('dashboard')
                ->with('error', 'No se pudo obtener la ubicación.');
        }

        $key = config('services.weather.key');

        // Consulta por coordenadas
        $current = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $key,
            'units' => 'metric',
            'lang' => 'es'
        ])->json();

        $forecast = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $key,
            'units' => 'metric',
            'lang' => 'es'
        ])->json();

        return view('dashboard.index', [
            'current' => $current,
            'forecast' => $forecast,
            'city' => $current['name'] ?? 'Ubicación desconocida'
        ]);
    }
}
