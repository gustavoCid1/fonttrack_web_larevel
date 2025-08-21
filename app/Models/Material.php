<?php

/**
 * Modelo Material - Gestión de inventario y control de materiales
 * 
 * Este modelo maneja la información de materiales, repuestos y suministros
 * del sistema de control de inventario. Proporciona funcionalidades para
 * el control de existencias, costos promedio, clasificación de materiales
 * y relación con ubicaciones geográficas para la gestión distribuida
 * del inventario en diferentes lugares del sistema.
 * 
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 * @since Laravel 10.x
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    
    // Configuración de la tabla de base de datos
    protected $table = 'tb_materiales';
    
    // Clave primaria personalizada
    protected $primaryKey = 'id_material'; 
    
    /**
     * Campos que pueden ser asignados de forma masiva
     * 
     * Define los atributos del material que pueden ser llenados
     * mediante asignación masiva, incluyendo identificación,
     * descripción, control de inventario, costos y ubicación.
     * Proporciona seguridad contra vulnerabilidades de mass assignment.
     */
    protected $fillable = [
        'clave_material',
        'descripcion',
        'generico',
        'clasificacion',
        'existencia',
        'costo_promedio',
        'id_lugar' 
    ];

    /**
     * ✅ CORREGIDO: Relación con el modelo Lugar
     * 
     * Establece la relación belongsTo con la tabla de lugares,
     * permitiendo identificar la ubicación donde se almacena
     * o gestiona cada material. Facilita el control de inventario
     * distribuido y la localización de recursos.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }
}