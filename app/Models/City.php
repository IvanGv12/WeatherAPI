<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable; // 👈 NECESARIO: Importar el Trait de Scout

class City extends Model
{
    use HasFactory;
    use Searchable; // ✅ CORRECCIÓN CLAVE: Usar el Trait para la indexación

    protected $fillable = [
        'name',
        'country',
        'postal_code',
        'user_id'
    ];

    /**
     * Opcional: Especificar la conexión de usuario.
     * Si necesitas que solo las ciudades del usuario autenticado se indexen o busquen.
     * Sin embargo, para la búsqueda AJAX de ciudades (maestra), este método debe manejarse con cuidado.
     * Si la tabla City es la lista MAESTRA de todas las ciudades, este método debería eliminarse.
     * Si la tabla City es la lista de CUIDADES FAVORITAS, es útil.
     */
    public function shouldBeSearchable(): bool
    {
        // ✅ CORRECCIÓN: Si esta tabla contiene las ciudades MAESTRAS para la búsqueda,
        // esto debería devolver true, o solo devolver true si estás indexando
        // las ciudades guardadas por el usuario.
        
        // Asumiendo que esta tabla contiene las ciudades MAESTRAS para el Autocomplete:
        return true; 
        
        // Si esta tabla solo contiene las ciudades FAVORITAS:
        // return $this->user_id !== null; 
    }

    /**
     * Datos que se enviarán a Algolia
     */
    public function toSearchableArray(): array
    {
        $array = [
            'name' => $this->name,
            // Incluir el ID de la base de datos para referenciar el registro
            'id' => $this->id, 
        ];

        // País si existe
        if ($this->country) {
            $array['country'] = $this->country;
        }

        // Postal si existe
        if ($this->postal_code) {
            $array['postal_code'] = $this->postal_code;
        }

        return $array;
    }
}