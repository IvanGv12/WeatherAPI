<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtiene el término de búsqueda de la consulta (ej. ?q=laravel)
        $query = $request->input('q');

        if (!$query) {
            // Si no hay término, puede devolver una vista vacía o redirigir
            return view('search.results', ['articles' => collect(), 'query' => null]);
        }

        // 2. Ejecuta la búsqueda usando Laravel Scout (Algolia)
        // Usamos paginate() para manejar resultados grandes
        $articles = Article::search($query)
                            ->paginate(10);
                            
        // 3. Devuelve los resultados a una vista
        return view('search.results', [
            'articles' => $articles,
            'query' => $query,
        ]);
    }
}