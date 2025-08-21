<?php

/**
 * Modelo Falla - Gestión de fallas vehiculares
 * 
 * Maneja el registro de fallas en vehículos, incluyendo información
 * del vehículo, materiales utilizados y usuarios involucrados.
 * 
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Falla extends Model
{
    use HasFactory;

    protected $table = 'tb_fallas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_lugar',
        'usuario_reporta_id',
        'nombre_usuario_reporta',
        'correo_usuario_reporta',
        'usuario_revisa_id',
        'nombre_usuario_revisa',
        'correo_usuario_revisa',
        'eco',
        'placas',
        'marca',
        'anio',
        'km',
        'fecha',
        'nombre_conductor',
        'descripcion',
        'observaciones',
        'autorizado_por',
        'reviso_por',
        'correo_destino',
        'material',
        'cantidad',
        'materials'
    ];

    // Conversión automática de tipos
    protected $casts = [
        'fecha' => 'date',
        'materials' => 'array',
        'cantidad' => 'integer',
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

    // Usuario admin que revisa
    public function usuarioRevisa()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_revisa_id', 'id_usuario');
    }

    /**
     * Procesa y formatea los materiales de la falla
     * Maneja tanto el array JSON como campos legacy
     */
    public function getMaterialesFormateadosAttribute()
    {
        // Procesar array de materials si existe
        if (!empty($this->materials) && is_array($this->materials)) {
            return collect($this->materials)->map(function ($material) {
                // Buscar ID del material en diferentes claves
                $materialId = $material['id'] ?? $material['id_material'] ?? $material['material_id'] ?? null;
                
                // Obtener descripción prioritariamente del array
                $descripcion = 'Material no especificado';
                
                if (!empty($material['descripcion'])) {
                    $descripcion = $material['descripcion'];
                } elseif ($materialId) {
                    $materialModel = \App\Models\Material::find($materialId);
                    if ($materialModel && $materialModel->descripcion) {
                        $descripcion = $materialModel->descripcion;
                    }
                } elseif (!empty($material['nombre'])) {
                    $descripcion = $material['nombre'];
                } elseif (!empty($material['name'])) {
                    $descripcion = $material['name'];
                }
                
                // Cantidad de diferentes claves posibles
                $cantidad = $material['cantidad'] ?? $material['qty'] ?? $material['quantity'] ?? 0;
                
                // Datos adicionales del material
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

    // Verifica si tiene materiales registrados
    public function tieneMateriales()
    {
        return !empty($this->materiales_formateados) || !empty($this->material);
    }

    // Nombre del primer material para mostrar en tablas
    public function getNombrePrimerMaterialAttribute()
    {
        $materiales = $this->materiales_formateados;
        
        if (empty($materiales)) {
            return $this->material ?? 'Sin material';
        }
        
        $primerMaterial = $materiales[0];
        $nombre = $primerMaterial['descripcion'];
        
        // Indicador si hay más materiales
        if (count($materiales) > 1) {
            $nombre .= ' (+' . (count($materiales) - 1) . ' más)';
        }
        
        return $nombre;
    }

    // Lista completa de materiales separados por coma
    public function getNombresMaterialesAttribute()
    {
        $materiales = $this->materiales_formateados;
        
        if (empty($materiales)) {
            return $this->material ?? 'Sin materiales';
        }
        
        return collect($materiales)
            ->pluck('descripcion')
            ->implode(', ');
    }

    // Suma total de cantidades
    public function getCantidadTotalMaterialesAttribute()
    {
        $materiales = $this->materiales_formateados;
        
        if (empty($materiales)) {
            return $this->cantidad ?? 0;
        }
        
        return collect($materiales)->sum('cantidad');
    }

    // Costo total de todos los materiales
    public function getCostoTotalMaterialesAttribute()
    {
        $materiales = $this->materiales_formateados;
        
        return collect($materiales)->sum('costo_total');
    }

    // Resumen corto para reportes
    public function getResumenAttribute()
    {
        $descripcion = $this->descripcion ?? 'Sin descripción';
        $vehiculo = $this->eco ? "ECO: {$this->eco}" : ($this->placas ? "Placas: {$this->placas}" : 'Vehículo no especificado');

        return "Falla - {$vehiculo} - " . substr($descripcion, 0, 50) . (strlen($descripcion) > 50 ? '...' : '');
    }

    // Contador de materiales
    public function getMaterialesCountAttribute()
    {
        return count($this->materiales_formateados);
    }

    // Información completa del vehículo
    public function getVehiculoInfoAttribute()
    {
        $info = [];

        if ($this->eco)
            $info[] = "ECO: {$this->eco}";
        if ($this->placas)
            $info[] = "Placas: {$this->placas}";
        if ($this->marca)
            $info[] = "Marca: {$this->marca}";
        if ($this->anio)
            $info[] = "Año: {$this->anio}";

        return !empty($info) ? implode(' | ', $info) : 'No especificado';
    }

    // Tiempo transcurrido desde creación
    public function getTiempoTranscurridoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Fecha formateada para mostrar
    public function getFechaCreacionAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    // Filtrar por lugar específico
    public function scopePorLugar($query, $idLugar)
    {
        return $query->where('id_lugar', $idLugar);
    }

    // Búsqueda general en múltiples campos
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('descripcion', 'like', "%{$termino}%")
                ->orWhere('observaciones', 'like', "%{$termino}%")
                ->orWhere('eco', 'like', "%{$termino}%")
                ->orWhere('placas', 'like', "%{$termino}%")
                ->orWhere('marca', 'like', "%{$termino}%")
                ->orWhere('nombre_conductor', 'like', "%{$termino}%")
                ->orWhere('material', 'like', "%{$termino}%")
                ->orWhere('autorizado_por', 'like', "%{$termino}%")
                ->orWhere('nombre_usuario_reporta', 'like', "%{$termino}%");
        });
    }
}