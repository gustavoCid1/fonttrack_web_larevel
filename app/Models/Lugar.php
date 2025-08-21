<?php

/**
 * Modelo Lugar - Ubicaciones del sistema
 * 
 * Maneja las ubicaciones geográficas donde se encuentran
 * los usuarios y materiales del sistema.
 * 
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    use HasFactory;

    // Configuración de tabla
    protected $table = 'tb_lugares';
    protected $primaryKey = 'id_lugar';
    public $timestamps = false;

    // Campos editables
    protected $fillable = [
        'nombre',
        'estado'
    ];

    /**
     * Usuarios asignados a este lugar
     */
    public function usuarios()
    {
        return $this->hasMany(Usuarios::class, 'id_lugar');
    }

    /**
     * Materiales almacenados en este lugar
     */
    public function materiales()
    {
        return $this->hasMany(Material::class, 'id_lugar');
    }
}