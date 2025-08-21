<?php

/**
 * Controlador Notificacion - Sistema de workflow para aprobación de fallas
 * 
 * Maneja el sistema completo de notificaciones pendientes que requieren
 * aprobación administrativa antes de convertirse en reportes oficiales.
 * Incluye APIs REST para frontend, generación de PDFs, envío de correos
 * y control de inventario con validaciones de stock en tiempo real.
 * 
 * @author jesus felipe aviles 
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 */

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Material;
use App\Models\Falla;
use App\Models\Usuarios;
use App\Models\Lugar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReporteFalla;
use Barryvdh\DomPDF\Facade\Pdf;

class NotificacionController extends Controller
{
    /**
     * API: Crear nueva notificación desde usuarios normales
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_lugar' => 'required|exists:tb_lugares,id_lugar',
            'usuario_reporta_id' => 'required|exists:tb_users,id_usuario',
            'nombre_usuario_reporta' => 'required|string',
            'correo_usuario_reporta' => 'required|email',
            'materials' => 'required|string', // JSON string
            // Otros campos son opcionales
        ]);

        try {
            DB::beginTransaction();

            // Decodificar materiales
            $materials = json_decode($request->materials, true);
            
            if (!$materials || !is_array($materials)) {
                return response()->json(['error' => 'Materiales inválidos'], 422);
            }

            // Verificar existencia de materiales
            foreach ($materials as $material) {
                $materialModel = Material::find($material['id']);
                if (!$materialModel) {
                    return response()->json(['error' => "Material ID {$material['id']} no encontrado"], 422);
                }
                
                if ($materialModel->existencia < $material['cantidad']) {
                    return response()->json([
                        'error' => "Stock insuficiente para {$materialModel->descripcion}. Disponible: {$materialModel->existencia}, Solicitado: {$material['cantidad']}"
                    ], 422);
                }
            }

            // Crear la notificación
            $notificacion = Notificacion::create([
                'id_lugar' => $request->id_lugar,
                'eco' => $request->eco,
                'placas' => $request->placas,
                'marca' => $request->marca,
                'anio' => $request->ano, // El formulario envía 'ano'
                'km' => $request->km,
                'fecha' => $request->fecha,
                'nombre_conductor' => $request->nombre_conductor,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'usuario_reporta_id' => $request->usuario_reporta_id,
                'nombre_usuario_reporta' => $request->nombre_usuario_reporta,
                'correo_usuario_reporta' => $request->correo_usuario_reporta,
                'materials' => $materials,
                'correo_destino' => $request->correo_destino,
                'estado' => 'pendiente'
            ]);

            DB::commit();

            Log::info('Nueva notificación creada', [
                'id_notificacion' => $notificacion->id_notificacion,
                'usuario_reporta' => $request->nombre_usuario_reporta,
                'lugar' => $request->id_lugar
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notificación enviada correctamente. Será revisada por un administrador.',
                'data' => $notificacion
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear notificación', [
                'error' => $e->getMessage(),
                'usuario' => $request->usuario_reporta_id
            ]);

            return response()->json([
                'error' => 'Error al enviar notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener notificaciones pendientes filtradas por lugar del usuario
     */
    public function getPendientes()
    {
        try {
            $user = Auth::user();
            $notificaciones = Notificacion::pendientes()
                ->where('id_lugar', $user->id_lugar) // Filtrar por lugar del usuario autenticado
                ->with(['lugar', 'usuarioReporta'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($notificacion) {
                    return [
                        'id' => $notificacion->id_notificacion,
                        'resumen' => $notificacion->resumen,
                        'usuario_reporta' => $notificacion->nombre_usuario_reporta,
                        'lugar' => $notificacion->lugar->nombre ?? 'Sin lugar',
                        'id_lugar' => $notificacion->id_lugar, // Incluir id_lugar para referencia
                        'fecha_creacion' => $notificacion->created_at?->format('d/m/Y H:i') ?? 'Sin fecha',
                        'tiempo_transcurrido' => $notificacion->tiempo_transcurrido,
                        'materiales_count' => count($notificacion->materials ?? []),
                        'vehiculo' => trim("{$notificacion->eco} {$notificacion->placas} {$notificacion->marca}"),
                        'descripcion_corta' => \Str::limit($notificacion->descripcion ?? '', 100)
                    ];
                });

            return response()->json([
                'success' => true,
                'notificaciones' => $notificaciones,
                'total' => $notificaciones->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener notificaciones pendientes', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error al cargar notificaciones'
            ], 500);
        }
    }

    /**
     * API: Obtener contador de notificaciones pendientes para badge
     */
    public function getContador()
    {
        try {
            $user = Auth::user();
            $count = Notificacion::pendientes()
                ->where('id_lugar', $user->id_lugar) // Filtrar por lugar del usuario autenticado
                ->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener contador'
            ], 500);
        }
    }

    /**
     * API: Obtener detalles completos de una notificación específica
     */
    public function show($id)
    {
        try {
            $notificacion = Notificacion::with(['lugar', 'usuarioReporta'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'notificacion' => [
                    'id' => $notificacion->id_notificacion,
                    'id_lugar' => $notificacion->id_lugar,
                    'lugar_nombre' => $notificacion->lugar->nombre ?? '',
                    'eco' => $notificacion->eco,
                    'placas' => $notificacion->placas,
                    'marca' => $notificacion->marca,
                    'anio' => $notificacion->anio,
                    'km' => $notificacion->km,
                    'fecha' => $notificacion->fecha ? $notificacion->fecha->format('Y-m-d') : '',
                    'nombre_conductor' => $notificacion->nombre_conductor,
                    'descripcion' => $notificacion->descripcion,
                    'observaciones' => $notificacion->observaciones,
                    'usuario_reporta' => $notificacion->nombre_usuario_reporta,
                    'correo_reporta' => $notificacion->correo_usuario_reporta,
                    'correo_destino' => $notificacion->correo_destino,
                    'materiales' => $notificacion->materiales_formateados,
                    'fecha_creacion' => $notificacion->created_at?->format('d/m/Y H:i:s') ?? 'Sin fecha',
                    'estado' => $notificacion->estado
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Notificación no encontrada'
            ], 404);
        }
    }

    /**
     * API: Aprobar notificación y convertir a falla con desconteo de materiales
     */
    public function aprobar(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string',
            'comentarios' => 'required|email|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Verificar contraseña del admin
            $admin = auth()->user();
            if (!Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'error' => 'Contraseña incorrecta'
                ], 422);
            }

            // Verificar que sea admin
            if ($admin->tipo_usuario !== 1) {
                return response()->json([
                    'error' => 'Solo administradores pueden aprobar notificaciones'
                ], 403);
            }

            $notificacion = Notificacion::findOrFail($id);

            // Verificar que esté pendiente
            if (!$notificacion->isPendiente()) {
                return response()->json([
                    'error' => 'Esta notificación ya fue procesada'
                ], 422);
            }

            // Verificar stock de materiales nuevamente
            if ($notificacion->materials && is_array($notificacion->materials)) {
                foreach ($notificacion->materials as $materialData) {
                    // Buscar ID en todas las posibles claves
                    $materialId = null;
                    if (isset($materialData['id'])) {
                        $materialId = $materialData['id'];
                    } elseif (isset($materialData['id_material'])) {
                        $materialId = $materialData['id_material'];
                    } elseif (isset($materialData['material_id'])) {
                        $materialId = $materialData['material_id'];
                    }
                    
                    // Buscar cantidad en todas las posibles claves
                    $cantidad = 0;
                    if (isset($materialData['cantidad'])) {
                        $cantidad = $materialData['cantidad'];
                    } elseif (isset($materialData['qty'])) {
                        $cantidad = $materialData['qty'];
                    } elseif (isset($materialData['quantity'])) {
                        $cantidad = $materialData['quantity'];
                    }
                    
                    if (!$materialId) {
                        Log::warning('Material sin ID encontrado - será ignorado', [
                            'material_data' => $materialData,
                            'notificacion_id' => $notificacion->id_notificacion
                        ]);
                        continue;
                    }

                    $material = Material::find($materialId);
                    if (!$material) {
                        Log::warning('Material no encontrado en BD - será ignorado', [
                            'material_id' => $materialId,
                            'notificacion_id' => $notificacion->id_notificacion
                        ]);
                        continue;
                    }
                    
                    if ($material->existencia < $cantidad) {
                        return response()->json([
                            'error' => "Stock insuficiente para {$material->descripcion}. Disponible: {$material->existencia}, Requerido: {$cantidad}"
                        ], 422);
                    }
                }

                // Descontar del stock
                foreach ($notificacion->materials as $materialData) {
                    $materialId = null;
                    $cantidad = 0;
                    
                    // Buscar ID
                    if (isset($materialData['id'])) {
                        $materialId = $materialData['id'];
                    } elseif (isset($materialData['id_material'])) {
                        $materialId = $materialData['id_material'];
                    } elseif (isset($materialData['material_id'])) {
                        $materialId = $materialData['material_id'];
                    }
                    
                    // Buscar cantidad
                    if (isset($materialData['cantidad'])) {
                        $cantidad = $materialData['cantidad'];
                    } elseif (isset($materialData['qty'])) {
                        $cantidad = $materialData['qty'];
                    } elseif (isset($materialData['quantity'])) {
                        $cantidad = $materialData['quantity'];
                    }
                    
                    // Solo procesar si tenemos ID y cantidad válidos
                    if ($materialId && $cantidad > 0) {
                        $material = Material::find($materialId);
                        if ($material) {
                            $stockAnterior = $material->existencia;
                            $material->decrement('existencia', $cantidad);
                            
                            Log::info('Stock descontado por aprobación', [
                                'material_id' => $material->id_material,
                                'material_clave' => $material->clave_material ?? 'N/A',
                                'cantidad_descontada' => $cantidad,
                                'stock_anterior' => $stockAnterior,
                                'stock_nuevo' => $material->existencia,
                                'notificacion_id' => $notificacion->id_notificacion
                            ]);
                        }
                    }
                }
            }

            // Crear falla directamente
            $materialResumen = [];
            $cantidadTotal = 0;
            
            if ($notificacion->materials && is_array($notificacion->materials)) {
                foreach ($notificacion->materials as $materialData) {
                    $materialId = $materialData['id'] ?? $materialData['id_material'] ?? $materialData['material_id'] ?? null;
                    $cantidad = $materialData['cantidad'] ?? $materialData['qty'] ?? $materialData['quantity'] ?? 0;
                    
                    if ($materialId && $cantidad > 0) {
                        $material = Material::find($materialId);
                        if ($material) {
                            $materialResumen[] = $material->descripcion . " (" . $cantidad . ")";
                            $cantidadTotal += $cantidad;
                        }
                    }
                }
            }

            $falla = DB::table('tb_fallas')->insertGetId([
                'id_lugar' => $notificacion->id_lugar,
                'usuario_reporta_id' => $notificacion->usuario_reporta_id,
                'nombre_usuario_reporta' => $notificacion->nombre_usuario_reporta,
                'correo_usuario_reporta' => $notificacion->correo_usuario_reporta,
                'usuario_revisa_id' => $admin->id_usuario ?? $admin->id,
                'nombre_usuario_revisa' => $admin->nombre ?? $admin->name,
                'correo_usuario_revisa' => $admin->correo ?? $admin->email,
                'eco' => $notificacion->eco,
                'placas' => $notificacion->placas,
                'marca' => $notificacion->marca,
                'anio' => $notificacion->anio,
                'km' => $notificacion->km,
                'fecha' => $notificacion->fecha,
                'nombre_conductor' => $notificacion->nombre_conductor,
                'descripcion' => $notificacion->descripcion,
                'observaciones' => $notificacion->observaciones,
                'autorizado_por' => $admin->nombre ?? $admin->name,
                'reviso_por' => $admin->nombre ?? $admin->name,
                'correo_destino' => $request->comentarios,
                'material' => implode(', ', $materialResumen),
                'cantidad' => $cantidadTotal,
                'materials' => json_encode($notificacion->materials),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Generar y enviar PDF
            $correoDestino = $request->comentarios;
            
            try {
                $pdfContent = $this->generarPDF($falla, $notificacion);
                $this->enviarCorreoNotificacion($falla, $pdfContent, $correoDestino, $notificacion);
                
                Log::info('PDF enviado por aprobación de notificación', [
                    'falla_id' => $falla,
                    'correo_destino' => $correoDestino,
                    'notificacion_id' => $notificacion->id_notificacion
                ]);
            } catch (\Exception $e) {
                Log::error('Error al enviar PDF por notificación', [
                    'error' => $e->getMessage(),
                    'falla_id' => $falla,
                    'correo_destino' => $correoDestino
                ]);
                // No fallar toda la operación por el email
            }

            // Eliminar la notificación después de procesar
            $notificacion->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Notificación aprobada correctamente. Se ha creado el reporte de falla y se envió por correo.',
                'falla_id' => $falla
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al aprobar notificación', [
                'notificacion_id' => $id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al aprobar notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF del reporte de falla desde notificación aprobada
     */
    private function generarPDF($reporteId, $notificacion)
    {
        try {
            $reporte = DB::table('tb_fallas')->where('id', $reporteId)->first();
            $lugar = Lugar::find($reporte->id_lugar);
            $nombreLugar = $lugar ? $lugar->nombre : 'No especificado';

            $materials = [];
            if ($reporte->materials) {
                $materials = is_string($reporte->materials) ? json_decode($reporte->materials, true) : $reporte->materials;
                $materials = is_array($materials) ? $materials : [];
            }

            $data = [
                'lugar' => $nombreLugar,
                'eco' => $reporte->eco ?? '',
                'placas' => $reporte->placas ?? '',
                'marca' => $reporte->marca ?? '',
                'anio' => $reporte->anio ?? '',
                'km' => $reporte->km ?? '',
                'fecha' => $reporte->fecha ?? '',
                'nombre_conductor' => $reporte->nombre_conductor ?? '',
                'descripcion' => $reporte->descripcion ?? '',
                'observaciones' => $reporte->observaciones ?? '',
                'autorizado_por' => $reporte->autorizado_por ?? '',
                'reviso_por' => $reporte->reviso_por ?? '',
                'materials' => $materials,
            ];

            $pdf = Pdf::loadView('reporte_fallo', compact('reporte', 'data'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'Arial',
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => false,
                    'chroot' => public_path(),
                ]);

            return $pdf->output();

        } catch (\Exception $e) {
            Log::error('Error al generar PDF de notificación: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enviar correo con PDF adjunto de notificación aprobada
     */
    private function enviarCorreoNotificacion($reporteId, $pdfContent, $correoDestino, $notificacion)
    {
        try {
            $reporte = DB::table('tb_fallas')->where('id', $reporteId)->first();
            
            $fallaObject = (object) [
                'id' => $reporte->id,
                'id_lugar' => $reporte->id_lugar,
                'eco' => $reporte->eco,
                'placas' => $reporte->placas,
                'marca' => $reporte->marca,
                'anio' => $reporte->anio,
                'km' => $reporte->km,
                'fecha' => $reporte->fecha,
                'nombre_conductor' => $reporte->nombre_conductor,
                'descripcion' => $reporte->descripcion,
                'observaciones' => $reporte->observaciones,
                'autorizado_por' => $reporte->autorizado_por,
                'reviso_por' => $reporte->reviso_por,
                'materials' => $reporte->materials,
            ];

            $admin = auth()->user();
            $adminEmail = $admin->correo ?? $admin->email ?? 'admin@example.com';

            Mail::to($correoDestino)
                ->cc($adminEmail)
                ->send(new ReporteFalla($fallaObject, $pdfContent));

            Log::info('Correo de notificación aprobada enviado exitosamente', [
                'correo_destino' => $correoDestino,
                'falla_id' => $reporteId,
                'notificacion_id' => $notificacion->id_notificacion
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de notificación: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * API: Rechazar notificación con comentarios administrativos
     */
    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'comentarios' => 'required|string|max:500'
        ]);

        try {
            $admin = auth()->user();

            // Verificar que sea admin
            if ($admin->tipo_usuario !== 1) {
                return response()->json([
                    'error' => 'Solo administradores pueden rechazar notificaciones'
                ], 403);
            }

            $notificacion = Notificacion::findOrFail($id);

            // Verificar que esté pendiente
            if (!$notificacion->isPendiente()) {
                return response()->json([
                    'error' => 'Esta notificación ya fue procesada'
                ], 422);
            }

            // Rechazar notificación (se mantiene en la tabla)
            $notificacion->rechazar($admin, $request->comentarios);

            return response()->json([
                'success' => true,
                'message' => 'Notificación rechazada correctamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al rechazar notificación', [
                'notificacion_id' => $id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error al rechazar notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener notificaciones rechazadas para auditoría
     */
    public function getRechazadas()
    {
        try {
            $user = Auth::user();
            $notificaciones = Notificacion::rechazadas()
                ->where('id_lugar', $user->id_lugar) // Filtrar por lugar del usuario autenticado
                ->with(['lugar', 'usuarioReporta', 'usuarioAprueba'])
                ->orderBy('fecha_aprobacion', 'desc')
                ->take(50) // Últimas 50
                ->get()
                ->map(function ($notificacion) {
                    return [
                        'id' => $notificacion->id_notificacion,
                        'resumen' => $notificacion->resumen,
                        'usuario_reporta' => $notificacion->nombre_usuario_reporta,
                        'lugar' => $notificacion->lugar->nombre ?? 'Sin lugar',
                        'fecha_rechazo' => $notificacion->fecha_aprobacion?->format('d/m/Y H:i') ?? 'Sin fecha',
                        'rechazada_por' => $notificacion->nombre_usuario_aprueba,
                        'comentarios' => $notificacion->comentarios_admin
                    ];
                });

            return response()->json([
                'success' => true,
                'notificaciones_rechazadas' => $notificaciones
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar notificaciones rechazadas'
            ], 500);
        }
    }

    /**
     * API: Eliminar notificaciones rechazadas para limpieza del sistema
     */
    public function limpiarRechazadas()
    {
        try {
            $admin = auth()->user();

            if ($admin->tipo_usuario !== 1) {
                return response()->json([
                    'error' => 'Solo administradores pueden realizar esta acción'
                ], 403);
            }

            $count = Notificacion::rechazadas()
                ->where('id_lugar', $admin->id_lugar) // Filtrar por lugar del usuario autenticado
                ->count();
            Notificacion::rechazadas()
                ->where('id_lugar', $admin->id_lugar) // Filtrar por lugar del usuario autenticado
                ->delete();

            Log::info('Notificaciones rechazadas eliminadas', [
                'cantidad' => $count,
                'admin_id' => $admin->id_usuario
            ]);

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$count} notificaciones rechazadas."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al limpiar notificaciones'
            ], 500);
        }
    }
}