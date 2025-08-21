<?php

/**
 * Modelo Vehiculo - Catálogo de vehículos
 * 
 * Maneja el registro completo de vehículos del sistema.
 * Incluye información técnica, estado operativo y ubicación.
 * 
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'tb_vehiculos';
    protected $primaryKey = 'id';
    public $timestamps = true;

    // Campos editables del vehículo
    protected $fillable = [
        'id_lugar',
        'eco',
        'placas',
        'marca',
        'anio',
        'kilometraje',
        'conductor_habitual',
        'modelo',
        'color',
        'estatus'
    ];

    // Conversión automática de tipos
    protected $casts = [
        'kilometraje' => 'integer'
    ];

    /**
     * Relación con lugar donde está asignado
     */
    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }

    /**
     * Solo vehículos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estatus', 'activo');
    }

    /**
     * Vehículos de un lugar específico
     */
    public function scopeDelLugar($query, $idLugar)
    {
        return $query->where('id_lugar', $idLugar);
    }

    /**
     * Búsqueda general en campos principales
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('eco', 'like', "%{$termino}%")
                ->orWhere('placas', 'like', "%{$termino}%")
                ->orWhere('marca', 'like', "%{$termino}%")
                ->orWhere('modelo', 'like', "%{$termino}%")
                ->orWhere('conductor_habitual', 'like', "%{$termino}%");
        });
    }
}