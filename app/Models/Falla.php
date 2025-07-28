<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Falla extends Model
{
    use HasFactory;

    protected $table = 'tb_fallas'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Clave primaria

    protected $fillable = [
        'id_lugar',
        'eco',
        'placas',
        'marca',
        'anio',              // Almacenamos 'ano' en la columna 'anio'
        'km',
        'fecha',
        'nombre_conductor',
        'descripcion',
        'observaciones',
        'autorizado_por',
        'reviso_por',
        'correo_destino',
        'material',          // Resumen de materiales (opcional)
        'cantidad',          // Total de cantidad descontada
        'materials'          // JSON original de materiales
    ];

    // Relación con la tabla Lugar
    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }
}
