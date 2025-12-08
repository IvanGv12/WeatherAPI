<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{
    public function index(WeatherService $weather)
    {
        // 1. Obtener solo las ciudades guardadas por el usuario autenticado.
        $cities = City::where('user_id', Auth::id())->get();

        // 2. Obtener clima resumido para cada ciudad usando el servicio WeatherService.
        $data = $cities->map(function ($city) use ($weather) {
            $info = $weather->current($city->name);
            
            return [
                'name' => $city->name,
                'id' => $city->id,
                'temp' => $info['main']['temp'] ?? 'N/A',
                'desc' => ucfirst($info['weather'][0]['description'] ?? 'Sin datos'),
                'country' => $city->country
            ];
        });

        // 3. Retorna la vista con los datos de las ciudades y su clima.
        return view('cities.index', compact('data'));
    }

    public function store(Request $request)
    {
        // 1. Validar que el campo 'name' es obligatorio.
        $request->validate([
            'name' => 'required|string'
        ]);
        
        // --- LOGICA DE VERIFICACIÓN DE DUPLICADOS ---
        $isAjax = $request->expectsJson() || $request->ajax();
        
        $exists = City::where('user_id', Auth::id())
                      ->where('name', $request->name)
                      ->exists();

        if ($exists) {
            $message = 'Esta ciudad ya está en tus favoritas.';
            
            if ($isAjax) {
                // 🛑 Devuelve JSON con código 409 Conflict si es AJAX
                return response()->json(['message' => $message], 409);
            }
            return redirect()->back()->with('error', $message);
        }

        // 2. Crear un nuevo registro de ciudad asociado al usuario autenticado.
        City::create([
            'name' => $request->name,
            'country' => $request->country, 
            // 🛑 'postal_code' es opcional, lo mantengo aquí si viene en la solicitud AJAX
            'postal_code' => $request->postal_code, 
            'user_id' => Auth::id()
        ]);

        // 3. Redirigir o responder con JSON ✅ CORRECCIÓN CLAVE
        $message = 'Ciudad guardada con éxito.';
        
        if ($isAjax) {
            // ✅ Devuelve JSON con código 201 Created si es AJAX
            return response()->json(['message' => $message], 201);
        }
        
        return redirect()->back()->with('success', $message);
    }

    public function destroy($id)
    {
        // 1. Eliminar la ciudad: Asegura que solo se pueda eliminar una ciudad si pertenece 
        // al usuario autenticado (medida de seguridad crítica).
        City::where('id', $id)->where('user_id', Auth::id())->delete();
        
        // 2. Redirigir a la página anterior con un mensaje de éxito.
        return redirect()->back()->with('success', 'Ciudad eliminada');
    }
}