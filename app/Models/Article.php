<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    // *******************************************************************
    // 1. TRAITS
    // *******************************************************************
    use Searchable, HasFactory; 

    // Campos que se pueden asignar masivamente (Ajusta a tus campos reales de la tabla)
    protected $fillable = [
        'title',
        'content', 
        'slug',
        'published_at',
    ];

    /**
     * Define los datos que se enviarán al índice de búsqueda de Algolia.
     * * @return array
     */
    public function toSearchableArray(): array
    {
        // Devuelve un arreglo con los datos exactos que quieres indexar.
        return [
            // Mantener 'id' es crucial para la sincronización y es un índice primario en Algolia.
            'objectID' => $this->id, // Scout usa 'objectID' internamente
            
            // Campos de texto para búsqueda
            'title' => $this->title,
            'content' => $this->content, 
            
            // Campos de filtrado o display
            'slug' => $this->slug,
            'published_at' => $this->published_at,
        ];
    }
}