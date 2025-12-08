@extends('layouts.app') 

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">
        Resultados de búsqueda @if($query) para: "{{ $query }}" @endif
    </h1>

    @if ($articles->isEmpty())
        <p class="text-gray-600">No se encontraron artículos que coincidan con su búsqueda.</p>
    @else
        <p class="text-gray-500 mb-6">Se encontraron {{ $articles->total() }} resultados.</p>
        
        <div class="space-y-4">
            @foreach ($articles as $article)
                <div class="border p-4 rounded-lg shadow-sm">
                    <h2 class="text-xl font-semibold text-blue-600">{{ $article->title }}</h2>
                    <p class="text-gray-700 mt-1">{{ Str::limit($article->content, 150) }}</p>
                    <a href="/articles/{{ $article->slug }}" class="text-sm text-indigo-500 hover:underline">Leer más</a>
                </div>
            @endforeach
        </div>
        
        {{-- Muestra los enlaces de paginación proporcionados por Scout --}}
        <div class="mt-8">
            {{ $articles->appends(['q' => $query])->links() }}
        </div>
    @endif
</div>
@endsection