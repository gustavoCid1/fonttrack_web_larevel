<?php

/**
 * Modelo Usuarios - Gestión de autenticación y usuarios del sistema
 * 
 * Este modelo extiende la funcionalidad de autenticación de Laravel
 * para gestionar usuarios del sistema de control de fallas vehiculares.
 * Incluye manejo de tipos de usuario, relaciones con lugares,
 * encriptación automática de contraseñas y gestión de fotografías
 * de perfil con rutas dinámicas y fallback a imagen por defecto.
 * 
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 * @since Laravel 10.x
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Usuarios extends Authenticatable
{
    use HasFactory, Notifiable;

    // Configuración de la tabla de base de datos
    protected $table = 'tb_users';

    // Clave primaria personalizada
    protected $primaryKey = 'id_usuario';

    // Deshabilitar timestamps automáticos de Laravel
    public $timestamps = false;

    /**
     * Campos que pueden ser asignados de forma masiva
     * 
     * Define los atributos que pueden ser llenados mediante
     * asignación masiva, proporcionando seguridad contra
     * vulnerabilidades de mass assignment.
     */
    protected $fillable = [
        'nombre',
        'correo',
        'password',
        'tipo_usuario',
        'foto_usuario',
        'id_lugar',
    ];

    /**
     * Campos que deben ocultarse en serialización
     * 
     * Protege información sensible como contraseñas
     * y tokens cuando el modelo se convierte a JSON
     * o array para respuestas de API.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos accesorios que se incluyen automáticamente
     * 
     * Define atributos virtuales que se calculan dinámicamente
     * y se incluyen en las respuestas JSON del modelo.
     */
    protected $appends = ['foto_usuario_url'];

    /**
     * Relación con el modelo Lugar
     * 
     * Establece la relación belongsTo con la tabla de lugares,
     * permitiendo acceder a la información de ubicación
     * asociada a cada usuario del sistema.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }

    /**
     * Mutador para el campo password
     * 
     * Automatiza la encriptación de contraseñas al momento
     * de asignar valores al campo password. Verifica si la
     * contraseña ya está encriptada para evitar doble encriptación
     * y aplica bcrypt cuando es necesario.
     * 
     * @param string $value Contraseña en texto plano o encriptada
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            if (Str::startsWith($value, '$2y$')) {
                $this->attributes['password'] = $value;
            } else {
                $this->attributes['password'] = bcrypt($value);
            }
        }
    }

    /**
     * Accesor para obtener la URL completa de la foto de usuario
     * 
     * Genera dinámicamente la URL completa de la imagen de perfil
     * del usuario. Verifica la existencia del archivo en el sistema
     * y proporciona una imagen por defecto como fallback cuando
     * no existe foto personalizada o el archivo no se encuentra.
     * 
     * @return string URL completa de la imagen de perfil
     */
    public function getFotoUsuarioUrlAttribute()
    {
        if ($this->foto_usuario && file_exists(public_path('img/' . $this->foto_usuario))) {
            return asset('img/' . $this->foto_usuario);
        }
        return asset('img/usuario_default.png');
    }
}