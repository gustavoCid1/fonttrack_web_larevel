<?php

/**
 * Modelo Notificacion - Sistema de workflow para fallas
 * 
 * Maneja el proceso de aprobación de fallas antes de convertirlas
 * en registros oficiales. Incluye estados, materiales y conversión
 * automática a fallas cuando se aprueban.
 * 
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'tb_notificaciones';
    protected $primaryKey = 'id_notificacion';

    protected $fillable = [
        // Lugar
        'id_lugar',

        // Datos del vehículo
        'eco',
        'placas',
        'marca',
        'anio',
        'km',
        'fecha',
        'nombre_conductor',

        // Descripción
        'descripcion',
        'observaciones',

        // Usuario que reporta
        'usuario_reporta_id',
        'nombre_usuario_reporta',
        'correo_usuario_reporta',

        // Materiales
        'material',
        'cantidad',
        'materials',

        // Administrativo
        'correo_destino',
        'estado',

        // Aprobación (solo se llenan cuando admin aprueba)
        'usuario_aprueba_id',
        'nombre_usuario_aprueba',
        'correo_usuario_aprueba',
        'autorizado_por',
        'reviso_por',
        'fecha_aprobacion',
        'comentarios_admin'
    ];

    protected $casts = [
        'fecha' => 'date',
        'materials' => 'array',
        'cantidad' => 'integer',
        'fecha_aprobacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con lugar
    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }

    // Usuario que reporta la falla
    public function usuarioReporta()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_reporta_id', 'id_usuario');
    }

    // Admin que aprueba la notificación
    public function usuarioAprueba()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_aprueba_id', 'id_usuario');
    }

    // Filtros por estado
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    // Filtro por lugar
    public function scopeDelLugar($query, $idLugar)
    {
        return $query->where('id_lugar', $idLugar);
    }

    // Notificaciones recientes (últimos 30 días)
    public function scopeRecientes($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays(30));
    }

    /**
     * Procesa y formatea los materiales para mostrar
     * Similar al modelo Falla pero adaptado para notificaciones
     */
    public function getMaterialesFormateadosAttribute()
    {
        // Procesar array de materials si existe
        if (!empty($this->materials) && is_array($this->materials)) {
            return collect($this->materials)->map(function ($material) {
                $materialId = $material['id'] ?? $material['id_material'] ?? $material['material_id'] ?? null;

                // Obtener descripción con prioridades
                $descripcion = 'Material no especificado';
                if (!empty($material['descripcion'])) {
                    $descripcion = $material['descripcion'];
                }
                elseif ($materialId) {
                    $materialModel = \App\Models\Material::find($materialId);
                    if ($materialModel && $materialModel->descripcion) {
                        $descripcion = $materialModel->descripcion;
                    }
                }
                elseif (!empty($material['nombre'])) {
                    $descripcion = $material['nombre'];
                } elseif (!empty($material['name'])) {
                    $descripcion = $material['name'];
                }

                $cantidad = $material['cantidad'] ?? $material['qty'] ?? $material['quantity'] ?? 0;
                $materialModel = $materialId ? \App\Models\Material::find($materialId) : null;

                return [
                    'id' => $materialId,
                    'descripcion' => $descripcion,
                    'cantidad' => $cantidad,
                    'clave' => $materialModel?->clave_material ?? 'N/A',
                    'costo_unitario' => $materialModel?->costo_promedio ?? 0,
                    'costo_total' => ($materialModel?->costo_promedio ?? 0) * $cantidad
                ];
            })->toArray();
        }

        // Fallback a campos legacy
        if (!empty($this->material)) {
            return [
                [
                    'id' => null,
                    'descripcion' => $this->material,
                    'cantidad' => $this->cantidad ?? 0,
                    'clave' => 'N/A',
                    'costo_unitario' => 0,
                    'costo_total' => 0
                ]
            ];
        }

        return [];
    }

    // Verificadores de estado
    public function isPendiente()
    {
        return $this->estado === 'pendiente';
    }

    public function isAprobada()
    {
        return $this->estado === 'aprobada';
    }

    public function isRechazada()
    {
        return $this->estado === 'rechazada';
    }

    /**
     * Aprueba la notificación y la convierte en falla
     * IMPORTANTE: Elimina la notificación después de crear la falla
     */
    public function aprobar($usuarioAdmin, $comentarios = null, $contraseña = null)
    {
        \DB::beginTransaction();

        try {
            // Crear la falla con todos los datos
            $fallaData = $this->toFallaData($usuarioAdmin, $contraseña);
            $falla = \App\Models\Falla::create($fallaData);

            // Log de la acción
            \Log::info('Notificación aprobada y convertida a falla', [
                'id_notificacion' => $this->id_notificacion,
                'id_falla_creada' => $falla->id,
                'admin_id' => $usuarioAdmin->id_usuario,
                'admin_nombre' => $usuarioAdmin->nombre,
                'comentarios' => $comentarios
            ]);

            // Eliminar la notificación
            $this->delete();

            \DB::commit();

            return $falla;

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Error al aprobar notificación', [
                'id_notificacion' => $this->id_notificacion,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Rechaza la notificación (se mantiene para auditoría)
     */
    public function rechazar($usuarioAdmin, $comentarios = null)
    {
        $this->update([
            'estado' => 'rechazada',
            'usuario_aprueba_id' => $usuarioAdmin->id_usuario,
            'nombre_usuario_aprueba' => $usuarioAdmin->nombre,
            'correo_usuario_aprueba' => $usuarioAdmin->correo,
            'fecha_aprobacion' => now(),
            'comentarios_admin' => $comentarios
        ]);

        \Log::info('Notificación rechazada', [
            'id_notificacion' => $this->id_notificacion,
            'admin_id' => $usuarioAdmin->id_usuario,
            'comentarios' => $comentarios
        ]);

        return $this;
    }

    /**
     * Convierte los datos de la notificación al formato de falla
     */
    public function toFallaData($usuarioAdmin, $contraseña = null)
    {
        // Preservar datos de materiales
        $materialsParaFalla = $this->materials;
        $materialParaFalla = $this->material;
        $cantidadParaFalla = $this->cantidad;

        // Generar resumen si hay array de materials
        if (!empty($this->materials) && is_array($this->materials)) {
            $materialesFormateados = $this->materiales_formateados;

            if (!empty($materialesFormateados)) {
                // Resumen textual para campo legacy
                $descripciones = array_column($materialesFormateados, 'descripcion');
                $materialParaFalla = implode(', ', array_slice($descripciones, 0, 3));
                if (count($descripciones) > 3) {
                    $materialParaFalla .= ' (+' . (count($descripciones) - 3) . ' más)';
                }

                $cantidadParaFalla = array_sum(array_column($materialesFormateados, 'cantidad'));
            }
        }

        return [
            // Datos básicos
            'id_lugar' => $this->id_lugar,
            'eco' => $this->eco,
            'placas' => $this->placas,
            'marca' => $this->marca,
            'anio' => $this->anio,
            'km' => $this->km,
            'fecha' => $this->fecha,
            'nombre_conductor' => $this->nombre_conductor,
            'descripcion' => $this->descripcion,
            'observaciones' => $this->observaciones,

            // Usuario que reportó
            'usuario_reporta_id' => $this->usuario_reporta_id,
            'nombre_usuario_reporta' => $this->nombre_usuario_reporta,
            'correo_usuario_reporta' => $this->correo_usuario_reporta,

            // Materiales en ambos formatos
            'materials' => $materialsParaFalla,
            'material' => $materialParaFalla,
            'cantidad' => $cantidadParaFalla,

            // Admin que aprueba
            'autorizado_por' => $usuarioAdmin->nombre,
            'reviso_por' => $contraseña ?? $usuarioAdmin->nombre,

            // Otros
            'correo_destino' => $this->correo_destino
        ];
    }

    // Badge HTML según el estado
    public function getEstadoBadgeAttribute()
    {
        return match ($this->estado) {
            'pendiente' => '<span class="badge bg-warning">Pendiente</span>',
            'aprobada' => '<span class="badge bg-success">Aprobada</span>',
            'rechazada' => '<span class="badge bg-danger">Rechazada</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>'
        };
    }

    // Tiempo transcurrido desde creación
    public function getTiempoTranscurridoAttribute()
    {
        return $this->created_at?->diffForHumans() ?? 'Sin fecha';
    }

    // Resumen corto para mostrar en listas
    public function getResumenAttribute()
    {
        $vehiculo = collect([$this->eco, $this->placas, $this->marca])
            ->filter()
            ->implode(' - ');

        $descripcion = \Str::limit($this->descripcion ?? 'Sin descripción', 50);

        return "{$vehiculo}: {$descripcion}";
    }

    // Eventos del modelo
    protected static function boot()
    {
        parent::boot();

        // Al crear, calcular resumen de materiales
        static::creating(function ($notificacion) {
            if ($notificacion->materials && is_array($notificacion->materials)) {
                $cantidadTotal = collect($notificacion->materials)->sum('cantidad');
                $materialesDescripcion = collect($notificacion->materials)
                    ->pluck('descripcion')
                    ->take(3)
                    ->implode(', ');

                $notificacion->cantidad = $cantidadTotal;
                $notificacion->material = $materialesDescripcion .
                    (count($notificacion->materials) > 3 ? '...' : '');
            }
        });

        // Log cuando cambia el estado
        static::updated(function ($notificacion) {
            if ($notificacion->isDirty('estado')) {
                \Log::info('Notificación cambió de estado', [
                    'id_notificacion' => $notificacion->id_notificacion,
                    'estado_anterior' => $notificacion->getOriginal('estado'),
                    'estado_nuevo' => $notificacion->estado,
                    'usuario_admin' => $notificacion->usuario_aprueba_id
                ]);
            }
        });

        // Log cuando se elimina
        static::deleted(function ($notificacion) {
            \Log::info('Notificación eliminada', [
                'id_notificacion' => $notificacion->id_notificacion,
                'estado_final' => $notificacion->estado,
                'razon' => $notificacion->estado === 'pendiente' ? 'Aprobada y convertida a falla' : 'Eliminación manual'
            ]);
        });
    }
}