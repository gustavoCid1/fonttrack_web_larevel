<?php

/**
 * Controlador Falla - Sistema de gestión de reportes de fallas
 * 
 * Maneja el sistema completo de fallas vehiculares incluyendo creación
 * de reportes, generación de PDFs, envío de correos y validaciones.
 * Incluye endpoints de API REST para integración con frontend JavaScript
 * y operaciones AJAX. Controla inventario mediante desconteo de materiales.
 * 
 * @author Jesús Felipe Avilez
 * @author Daniela Pérez Peralta
 * @version 2.0.1 - Corregido
 */

namespace App\Http\Controllers;

use App\Models\Falla;
use App\Models\Lugar;
use App\Models\Material;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReporteFalla;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FallaController extends Controller
{
    /**
     * Vista principal de reportes con filtros por lugar
     */
    public function index(Request $request)
    {
        $lugares = Lugar::all();
        $materiales = Material::all();
        $query = Falla::query();

        if ($request->has('id_lugar') && $request->id_lugar !== '') {
            $query->where('id_lugar', $request->id_lugar);
        }

        // Cargar TODOS los registros sin paginar
        $fallas = $query->with(['lugar'])->orderBy('created_at', 'desc')->get();

        return view('reportes_index', compact('fallas', 'lugares', 'materiales'));
    }

    /**
     * API: Obtener datos de una falla específica en formato JSON
     */
    public function show($id)
    {
        try {
            $falla = Falla::find($id);
            if (!$falla) {
                return response()->json(['error' => 'Falla no encontrada'], 404);
            }

            // ✅ OPTIMIZADO: Manejo simplificado de materials
            $materials = $this->procesarMaterials($falla->materials);

            return response()->json([
                'data' => [
                    'id_falla' => $falla->id,
                    'id_lugar' => $falla->id_lugar,
                    'usuario_reporta_id' => $falla->usuario_reporta_id ?? '',
                    'nombre_usuario_reporta' => $falla->nombre_usuario_reporta ?? '',
                    'correo_usuario_reporta' => $falla->correo_usuario_reporta ?? '',
                    'eco' => $falla->eco ?? '',
                    'placas' => $falla->placas ?? '',
                    'marca' => $falla->marca ?? '',
                    'anio' => $falla->anio ?? '',
                    'km' => $falla->km ?? '',
                    'fecha' => $falla->fecha ?? '',
                    'nombre_conductor' => $falla->nombre_conductor ?? '',
                    'descripcion' => $falla->descripcion ?? '',
                    'observaciones' => $falla->observaciones ?? '',
                    'autorizado_por' => $falla->autorizado_por ?? '',
                    'correo_destino' => $falla->correo_destino ?? '',
                ],
                'materials' => $materials
            ]);

        } catch (\Exception $e) {
            Log::error('Error en show falla: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener la falla'], 500);
        }
    }

    /**
     * API: Crear nueva falla con validaciones y desconteo de materiales
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        try {
            $validated = $request->validate([
                'id_lugar' => 'required|exists:tb_lugares,id_lugar',
                'usuario_reporta_id' => 'required',
                'nombre_usuario_reporta' => 'required|string|max:255',
                'correo_usuario_reporta' => 'required|email|max:255',
                'usuario_revisa_id' => 'required|exists:tb_users,id_usuario',
                'nombre_usuario_revisa' => 'required|string|max:255',
                'correo_usuario_revisa' => 'required|email|max:255',
                'materials' => 'required|json',
                'reviso_por' => 'required|string',
            ]);

            // Verificar que el usuario que revisa sea ADMIN y la contraseña sea correcta
            $usuarioRevisa = Usuarios::find($request->usuario_revisa_id);
            if (!$usuarioRevisa || $usuarioRevisa->tipo_usuario != 1) {
                return response()->json([
                    'errors' => ['usuario_revisa_id' => ['El usuario seleccionado no tiene permisos de administrador.']]
                ], 422);
            }

            if (!Hash::check($request->reviso_por, $usuarioRevisa->password)) {
                return response()->json([
                    'errors' => ['reviso_por' => ['La contraseña del usuario que revisa es incorrecta.']]
                ], 422);
            }

            $materials = json_decode($request->materials, true);
            if (empty($materials)) {
                return response()->json(['errors' => ['materials' => ['Debe incluir al menos un material']]], 422);
            }

            DB::beginTransaction();

            $materialResumen = [];
            $cantidadTotal = 0;

            foreach ($materials as $material) {
                $materialModel = Material::find($material['id']);
                if (!$materialModel) {
                    throw new \Exception("Material con ID {$material['id']} no encontrado");
                }

                if ($materialModel->existencia < $material['cantidad']) {
                    throw new \Exception("No hay suficiente existencia del material: {$materialModel->descripcion}");
                }

                $materialResumen[] = $materialModel->descripcion . " (" . $material['cantidad'] . ")";
                $cantidadTotal += $material['cantidad'];

                $materialModel->existencia -= $material['cantidad'];
                $materialModel->save();
            }

            // Crear falla con nombre del usuario revisor
            $falla = Falla::create([
                'id_lugar' => $request->id_lugar,
                'usuario_reporta_id' => $request->usuario_reporta_id,
                'nombre_usuario_reporta' => $request->nombre_usuario_reporta,
                'correo_usuario_reporta' => $request->correo_usuario_reporta,
                'eco' => $request->eco,
                'placas' => $request->placas,
                'marca' => $request->marca,
                'anio' => $request->anio,
                'km' => $request->km,
                'fecha' => $request->fecha,
                'nombre_conductor' => $request->nombre_conductor,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'autorizado_por' => $usuarioRevisa->nombre,
                'reviso_por' => $usuarioRevisa->nombre,
                'correo_destino' => $request->correo_destino,
                'material' => implode(', ', $materialResumen),
                'cantidad' => $cantidadTotal,
                'materials' => json_encode($materials),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Reporte creado exitosamente',
                'data' => ['id_falla' => $falla->id]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear falla: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar y mostrar PDF del reporte de falla
     */
    public function pdf($id)
    {
        try {
            Log::info("Generando PDF para falla ID: " . $id);

            $falla = Falla::find($id);

            if (!$falla) {
                Log::error("Falla no encontrada con ID: " . $id);
                return response()->json(['error' => 'Reporte no encontrado con ID: ' . $id], 404);
            }

            Log::info("Falla encontrada: " . json_encode($falla->toArray()));

            $lugar = Lugar::find($falla->id_lugar);
            $nombreLugar = $lugar ? $lugar->nombre : 'No especificado';

            // ✅ OPTIMIZADO: Usar método helper para procesar materials
            $materials = $this->procesarMaterials($falla->materials);

            // Preparar los datos en el formato que espera la vista
            $data = [
                'id_reporte' => $falla->id,
                'lugar' => $nombreLugar,
                'eco' => $falla->eco ?? '',
                'placas' => $falla->placas ?? '',
                'marca' => $falla->marca ?? '',
                'anio' => $falla->anio ?? '',
                'km' => $falla->km ?? '',
                'fecha' => $falla->fecha ?? '',
                'nombre_conductor' => $falla->nombre_conductor ?? '',
                'descripcion' => $falla->descripcion ?? '',
                'observaciones' => $falla->observaciones ?? '',
                'autorizado_por' => $falla->autorizado_por ?? '',
                'reviso_por' => $falla->reviso_por ?? $falla->autorizado_por ?? '',
                'materials' => $materials,
            ];

            Log::info("Datos preparados para PDF: " . json_encode($data));

            // Enviar datos a la vista
            $pdf = Pdf::loadView('reporte_fallo', compact('falla', 'data'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'Arial',
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => false,
                    'chroot' => public_path(),
                ]);

            return $pdf->stream('reporte_falla_' . $id . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error PDF: ' . $e->getMessage());
            Log::error('Error Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Error al generar PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Enviar reporte por correo electrónico con PDF adjunto
     */
    public function enviar($id, Request $request)
    {
        try {
            $request->validate(['correo_destino' => 'required|email']);

            $falla = Falla::find($id);
            if (!$falla) {
                return response()->json(['error' => 'Reporte no encontrado'], 404);
            }

            $lugar = Lugar::find($falla->id_lugar);
            $nombreLugar = $lugar ? $lugar->nombre : 'No especificado';

            // ✅ OPTIMIZADO: Usar método helper para procesar materials
            $materials = $this->procesarMaterials($falla->materials);

            // Preparar los datos para la vista
            $data = [
                'id_reporte' => $falla->id,
                'lugar' => $nombreLugar,
                'eco' => $falla->eco ?? '',
                'placas' => $falla->placas ?? '',
                'marca' => $falla->marca ?? '',
                'anio' => $falla->anio ?? '',
                'km' => $falla->km ?? '',
                'fecha' => $falla->fecha ?? '',
                'nombre_conductor' => $falla->nombre_conductor ?? '',
                'descripcion' => $falla->descripcion ?? '',
                'observaciones' => $falla->observaciones ?? '',
                'autorizado_por' => $falla->autorizado_por ?? '',
                'reviso_por' => $falla->reviso_por ?? $falla->autorizado_por ?? '',
                'materials' => $materials,
            ];

            // Usar la misma estructura que en el método pdf()
            $pdf = Pdf::loadView('reporte_fallo', compact('falla', 'data'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'Arial',
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => false,
                    'chroot' => public_path(),
                ]);

            Mail::to($request->correo_destino)
                ->cc(Auth::user()->email)
                ->send(new ReporteFalla($falla, $pdf->output()));

            return response()->json(['message' => 'Correo enviado exitosamente']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al enviar correo: ' . $e->getMessage());
            return response()->json(['message' => 'Error al enviar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Verificar contraseña del usuario autenticado
     */
    public function verificarPassword(Request $request)
    {
        try {
            $request->validate(['password' => 'required|string']);

            if (!Auth::check()) {
                return response()->json(['success' => false, 'error' => 'Usuario no autenticado'], 401);
            }

            if (Hash::check($request->password, Auth::user()->password)) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'nombre' => Auth::user()->nombre ?? Auth::user()->name,
                        'email' => Auth::user()->correo ?? Auth::user()->email
                    ]
                ]);
            }

            return response()->json(['success' => false, 'error' => 'Contraseña incorrecta'], 401);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al verificar password: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * API: Verificar contraseña de usuario específico para validación
     */
    public function verificarPasswordUsuario(Request $request)
    {
        try {
            $request->validate([
                'usuario_id' => 'required|integer',
                'password' => 'required|string'
            ]);

            $usuario = Usuarios::find($request->usuario_id);

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar que el usuario sea ADMIN
            if ($usuario->tipo_usuario != 1) {
                return response()->json([
                    'success' => false,
                    'error' => 'El usuario no tiene permisos de administrador'
                ], 403);
            }

            if (Hash::check($request->password, $usuario->password)) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $usuario->id_usuario,
                        'nombre' => $usuario->nombre,
                        'email' => $usuario->correo
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Contraseña incorrecta'
            ], 401);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al verificar password usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al verificar contraseña: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener lista de usuarios administradores
     */
    public function getUsuariosAdmin()
    {
        try {
            $usuarios = Usuarios::where('tipo_usuario', 1)
                ->select('id_usuario as id', 'nombre as name', 'correo as email')
                ->get();

            return response()->json(['usuarios' => $usuarios], 200);

        } catch (\Exception $e) {
            Log::error('Error al cargar usuarios admin: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al cargar usuarios admin',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener usuarios de un lugar específico
     */
    public function usuariosPorLugar($id_lugar)
    {
        try {
            $usuarios = Usuarios::where('id_lugar', $id_lugar)
                ->select('id_usuario as id', 'nombre as name', 'correo as email')
                ->get();

            return response()->json(['usuarios' => $usuarios], 200);

        } catch (\Exception $e) {
            Log::error('Error al cargar usuarios por lugar: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al cargar usuarios',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alias para compatibilidad de rutas
     */
    public function getUsuariosPorLugar($id)
    {
        return $this->usuariosPorLugar($id);
    }

    /**
     * API: Búsqueda de materiales para autocompletar
     */
    public function searchMaterials(Request $request)
    {
        try {
            $query = $request->input('query', '');
            $materiales = Material::where(function ($q) use ($query) {
                $q->where('clave_material', 'LIKE', "%{$query}%")
                    ->orWhere('descripcion', 'LIKE', "%{$query}%");
            })
                ->select('id_material', 'clave_material', 'descripcion', 'existencia')
                ->limit(50)
                ->get();

            return response()->json(['materiales' => $materiales], 200);

        } catch (\Exception $e) {
            Log::error('Error al buscar materiales: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al buscar materiales',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alias para vista de reportes
     */
    public function showReportes(Request $request)
    {
        return $this->index($request);
    }

    /**
     * API: Obtener todas las fallas con filtros para paginación JavaScript
     */
    public function getAllFallas(Request $request)
    {
        try {
            $query = Falla::query();

            // Aplicar filtros si existen
            if ($request->has('id_lugar') && $request->id_lugar !== '') {
                $query->where('id_lugar', $request->id_lugar);
            }

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre_conductor', 'LIKE', "%{$search}%")
                        ->orWhere('descripcion', 'LIKE', "%{$search}%")
                        ->orWhere('eco', 'LIKE', "%{$search}%")
                        ->orWhere('placas', 'LIKE', "%{$search}%")
                        ->orWhere('marca', 'LIKE', "%{$search}%")
                        ->orWhere('observaciones', 'LIKE', "%{$search}%")
                        ->orWhere('material', 'LIKE', "%{$search}%")
                        ->orWhere('autorizado_por', 'LIKE', "%{$search}%")
                        ->orWhere('nombre_usuario_reporta', 'LIKE', "%{$search}%");
                });
            }

            // Obtener TODOS los registros ordenados por fecha de creación
            $fallas = $query->with(['lugar'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $fallas,
                'total' => $fallas->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cargar todas las fallas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar fallas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ NUEVO: Método helper para procesar materials de forma consistente
     * Maneja tanto arrays como strings JSON y devuelve siempre un array
     */
    private function procesarMaterials($materials)
    {
        if (empty($materials)) {
            return [];
        }

        // Si ya es un array, devolverlo directamente
        if (is_array($materials)) {
            return $materials;
        }

        // Si es string, intentar decodificar JSON
        if (is_string($materials)) {
            $decoded = json_decode($materials, true);
            return is_array($decoded) ? $decoded : [];
        }

        // Si es otro tipo, devolver array vacío
        return [];
    }
}