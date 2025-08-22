<?php

/**
 * ============================================================================
 * CONTROLADOR PRINCIPAL DE MATERIALES - FONTTRACK SYSTEM
 * ============================================================================
 * 
 * Este controlador maneja todas las operaciones relacionadas con materiales:
 * 
 * • CRUD completo de materiales con filtrado por ubicación
 * • Gestión de inventario y control de existencias
 * • Generación de reportes de fallas en PDF
 * • Importación/exportación masiva de datos Excel (Kardex)
 * • Sistema de validación de contraseñas para autorizaciones
 * • Integración con vehículos y usuarios por ubicación
 * • Envío de notificaciones por correo electrónico
 * • Control de acceso basado en roles y ubicaciones
 * 
 * @author Gustavo Angel Cid Flores
 * @version 2.1.0
 * @since 2024
 * @package App\Http\Controllers
 * @requires Laravel 9+, PhpSpreadsheet, DomPDF
 * ============================================================================
 */

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Lugar;
use App\Models\Usuarios;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\ReporteFalla;

class MaterialController extends Controller
{
    /**
     * ========================================================================
     * VISTA PRINCIPAL CON FILTRADO POR UBICACIÓN
     * ========================================================================
     * 
     * Muestra la lista de materiales filtrada según el tipo de usuario:
     * - Administradores: Ven todos los materiales del sistema
     * - Usuarios regulares: Solo ven materiales de su ubicación asignada
     * 
     * @param Request $request - Parámetros de búsqueda y filtrado
     * @return \Illuminate\View\View - Vista con materiales filtrados
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $lugares = Lugar::all();
        $query = $request->input('query');

        /** FILTRADO OBLIGATORIO POR LUGAR DEL USUARIO */
        $materialesQuery = Material::with('lugar');
        
        /** FILTRO PRINCIPAL: Si no es admin (tipo_usuario != 1), SOLO ver materiales de su lugar */
        if ($user->tipo_usuario != 1) {
            if (!$user->id_lugar) {
                /** Si no tiene lugar asignado, no ve ningún material */
                $materialesQuery->whereRaw('1 = 0');
            } else {
                /** Solo materiales de su lugar */
                $materialesQuery->where('id_lugar', $user->id_lugar);
            }
        }
        /** Si es admin (tipo_usuario == 1), ve todos los materiales */
        
        /** Aplicar filtro de búsqueda si existe */
        if ($query) {
            $materialesQuery->where(function ($q) use ($query) {
                $q->where('clave_material', 'like', '%' . $query . '%')
                  ->orWhere('descripcion', 'like', '%' . $query . '%')
                  ->orWhere('generico', 'like', '%' . $query . '%')
                  ->orWhere('clasificacion', 'like', '%' . $query . '%')
                  ->orWhere('existencia', 'like', '%' . $query . '%')
                  ->orWhere('costo_promedio', 'like', '%' . $query . '%');
            });
        }
        
        /** Obtener materiales paginados */
        $materiales = $materialesQuery->orderBy('clave_material')->paginate(10);

        /** OBTENER VEHÍCULOS DEL LUGAR DEL USUARIO */
        $vehiculos = [];
        if ($user->id_lugar) {
            $vehiculos = Vehiculo::where('id_lugar', $user->id_lugar)
                ->where('estatus', 'activo')
                ->select('id', 'eco', 'placas', 'marca', 'anio', 'kilometraje')
                ->orderBy('eco')
                ->get();
        }

        return view('index_materiales', compact('materiales', 'lugares', 'vehiculos'));
    }

    /**
     * ========================================================================
     * OBTENER VEHÍCULOS FILTRADOS POR UBICACIÓN
     * ========================================================================
     * 
     * Retorna la lista de vehículos activos para una ubicación específica
     * con validación de permisos según el tipo de usuario.
     * 
     * @param int $id_lugar - ID de la ubicación
     * @return \Illuminate\Http\JsonResponse - Lista de vehículos en formato JSON
     */
    public function getVehiculosPorLugar($id_lugar)
    {
        try {
            $user = Auth::user();

            /** Verificar permisos */
            if ($user->tipo_usuario != 1 && $user->id_lugar != $id_lugar) {
                return response()->json(['error' => 'No tienes permisos para ver estos vehículos'], 403);
            }

            $vehiculos = Vehiculo::where('id_lugar', $id_lugar)
                ->where('estatus', 'activo')
                ->select('id', 'eco', 'placas', 'marca', 'anio', 'kilometraje')
                ->orderBy('eco')
                ->get();

            return response()->json(['vehiculos' => $vehiculos], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar vehículos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================================================
     * OBTENER DATOS ESPECÍFICOS DE VEHÍCULO POR ECO
     * ========================================================================
     * 
     * Busca un vehículo específico por su número ECO y retorna
     * sus datos completos incluyendo conductor habitual.
     * 
     * @param string $eco - Número ECO del vehículo
     * @return \Illuminate\Http\JsonResponse - Datos del vehículo
     */
    public function getVehiculoPorEco($eco)
    {
        try {
            $user = Auth::user();

            $vehiculo = Vehiculo::where('eco', $eco)
                ->when($user->tipo_usuario != 1 && $user->id_lugar, function ($q) use ($user) {
                    $q->where('id_lugar', $user->id_lugar);
                })
                ->select('id', 'eco', 'placas', 'marca', 'anio', 'kilometraje', 'conductor_habitual')
                ->first();

            if (!$vehiculo) {
                return response()->json(['error' => 'Vehículo no encontrado'], 404);
            }

            return response()->json(['vehiculo' => $vehiculo], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar vehículo',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================================================
     * CREAR NUEVO MATERIAL CON VALIDACIÓN DE UBICACIÓN
     * ========================================================================
     * 
     * Crea un nuevo material en el sistema con validaciones completas
     * de campos requeridos y verificación de ubicación válida.
     * 
     * @param Request $request - Datos del material a crear
     * @return \Illuminate\Http\JsonResponse - Respuesta de éxito o error
     */
    public function store(Request $request)
    {
        $request->validate([
            'clave_material' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'generico' => 'nullable|string|max:255',
            'clasificacion' => 'nullable|string|max:255',
            'existencia' => 'required|integer|min:0',
            'costo_promedio' => 'required|numeric|min:0',
            'id_lugar' => 'required|integer|exists:tb_lugares,id_lugar',
        ]);

        $material = Material::create($request->all());
        return response()->json(['message' => 'Material agregado correctamente', 'data' => $material], 201);
    }

    /**
     * ========================================================================
     * MOSTRAR MATERIAL ESPECÍFICO CON INFORMACIÓN DE UBICACIÓN
     * ========================================================================
     * 
     * Retorna los datos completos de un material específico
     * incluyendo información de la ubicación asignada.
     * 
     * @param int $id - ID del material
     * @return \Illuminate\Http\JsonResponse - Datos del material
     */
    public function show($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['error' => 'ID inválido'], 400);
        }

        $material = Material::with('lugar')->find($id);
        if (!$material) {
            return response()->json(['error' => 'Material no encontrado'], 404);
        }
        
        $materialData = $material->toArray();
        $materialData['lugar_nombre'] = $material->lugar ? $material->lugar->nombre : 'Sin lugar asignado';
        
        return response()->json(['data' => $materialData]);
    }

    /**
     * ========================================================================
     * PREPARAR MATERIAL PARA EDICIÓN
     * ========================================================================
     * 
     * Retorna los datos de un material para ser editado,
     * incluyendo información completa de la ubicación.
     * 
     * @param int $id - ID del material
     * @return \Illuminate\Http\JsonResponse - Datos para edición
     */
    public function edit($id)
    {
        $material = Material::with('lugar')->find($id);
        if (!$material) {
            return response()->json(['error' => 'Material no encontrado'], 404);
        }
        
        $materialData = $material->toArray();
        $materialData['lugar_nombre'] = $material->lugar ? $material->lugar->nombre : 'Sin lugar asignado';
        
        return response()->json(['data' => $materialData]);
    }

    /**
     * ========================================================================
     * ACTUALIZAR MATERIAL CON VALIDACIÓN COMPLETA
     * ========================================================================
     * 
     * Actualiza los datos de un material existente con validaciones
     * de campos requeridos y verificación de ubicación.
     * 
     * @param Request $request - Nuevos datos del material
     * @param int $id - ID del material a actualizar
     * @return \Illuminate\Http\JsonResponse - Respuesta de éxito o error
     */
    public function update(Request $request, $id)
    {
        $material = Material::find($id);
        if (!$material) {
            return response()->json(['error' => 'Material no encontrado'], 404);
        }

        $request->validate([
            'clave_material' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|required|string|max:255',
            'generico' => 'nullable|string|max:255',
            'clasificacion' => 'nullable|string|max:255',
            'existencia' => 'required|integer|min:0',
            'costo_promedio' => 'required|numeric|min:0',
            'id_lugar' => 'required|integer|exists:tb_lugares,id_lugar',
        ]);

        $material->update($request->all());
        return response()->json(['message' => 'Material actualizado correctamente', 'data' => $material]);
    }

    /**
     * ========================================================================
     * ELIMINAR MATERIAL DEL SISTEMA
     * ========================================================================
     * 
     * Elimina permanentemente un material del sistema.
     * 
     * @param int $id - ID del material a eliminar
     * @return \Illuminate\Http\JsonResponse - Confirmación de eliminación
     */
    public function destroy($id)
    {
        Material::findOrFail($id)->delete();
        return response()->json(['message' => 'Material eliminado']);
    }

    /**
     * ========================================================================
     * AUMENTAR EXISTENCIA DE MATERIAL
     * ========================================================================
     * 
     * Incrementa la cantidad en stock de un material específico
     * con validación de cantidad mínima.
     * 
     * @param Request $request - Cantidad a aumentar
     * @param int $id - ID del material
     * @return \Illuminate\Http\JsonResponse - Confirmación de actualización
     */
    public function aumentarExistencia(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        $material->existencia += $request->cantidad;
        $material->save();

        return response()->json(['message' => 'Existencia aumentada correctamente']);
    }

    /**
     * ========================================================================
     * CREAR REPORTE DE FALLA CON GENERACIÓN DE PDF
     * ========================================================================
     * 
     * Crea un reporte de falla completo con:
     * - Validación de contraseña del usuario supervisor
     * - Descuento automático de existencias de materiales
     * - Generación de PDF del reporte
     * - Envío opcional por correo electrónico
     * 
     * @param Request $request - Datos completos del reporte
     * @return \Illuminate\Http\JsonResponse - Respuesta con URL del PDF generado
     */
    public function crearReporteFalla(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

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
            'correo_destino' => 'nullable|email',
            'enviar_correo' => 'nullable|in:true,false,1,0',
        ]);

        $enviarCorreo = in_array($request->enviar_correo, ['true', '1', 1, true], true);

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

        try {
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

            $reporteFalla = DB::table('tb_fallas')->insertGetId([
                'id_lugar' => $request->id_lugar,
                'usuario_reporta_id' => $request->usuario_reporta_id,
                'nombre_usuario_reporta' => $request->nombre_usuario_reporta,
                'correo_usuario_reporta' => $request->correo_usuario_reporta,
                'usuario_revisa_id' => $request->usuario_revisa_id,
                'nombre_usuario_revisa' => $request->nombre_usuario_revisa,
                'correo_usuario_revisa' => $request->correo_usuario_revisa,
                'eco' => $request->eco,
                'placas' => $request->placas,
                'marca' => $request->marca,
                'anio' => $request->anio,
                'km' => $request->km,
                'fecha' => $request->fecha,
                'nombre_conductor' => $request->nombre_conductor,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'autorizado_por' => $request->nombre_usuario_reporta,
                'reviso_por' => $usuarioRevisa->nombre,
                'correo_destino' => $request->correo_destino,
                'material' => implode(', ', $materialResumen),
                'cantidad' => $cantidadTotal,
                'materials' => json_encode($materials),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            $pdfContent = $this->generarPDF($reporteFalla, $request);

            if ($enviarCorreo && $request->correo_destino) {
                $this->enviarCorreo($reporteFalla, $pdfContent, $request->correo_destino);
            }

            return response()->json([
                'message' => 'Reporte creado exitosamente',
                'data' => ['id_falla' => $reporteFalla],
                'pdf_url' => route('materials.pdf.falla', $reporteFalla)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear reporte de falla: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================================================
     * GENERAR PDF DEL REPORTE DE FALLA
     * ========================================================================
     * 
     * Genera un archivo PDF personalizado del reporte de falla
     * con todos los datos del vehículo, materiales y firmas.
     * 
     * @param int $reporteId - ID del reporte a generar
     * @param Request $request - Datos adicionales del reporte
     * @return string - Contenido del PDF generado
     */
    private function generarPDF($reporteId, $request)
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
                'id_reporte' => $reporte->id,
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
            Log::error('Error al generar PDF: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ========================================================================
     * ENVIAR REPORTE POR CORREO ELECTRÓNICO
     * ========================================================================
     * 
     * Envía el reporte de falla generado por correo electrónico
     * con copia al usuario autenticado.
     * 
     * @param int $reporteId - ID del reporte
     * @param string $pdfContent - Contenido del PDF
     * @param string $correoDestino - Correo de destino
     * @throws \Exception Si hay error en el envío
     */
    private function enviarCorreo($reporteId, $pdfContent, $correoDestino)
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

            Mail::to($correoDestino)
                ->cc(Auth::user()->email)
                ->send(new ReporteFalla($fallaObject, $pdfContent));

            Log::info('Correo enviado exitosamente a: ' . $correoDestino);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ========================================================================
     * MOSTRAR PDF DE REPORTE DE FALLA EN NAVEGADOR
     * ========================================================================
     * 
     * Genera y muestra el PDF de un reporte de falla específico
     * directamente en el navegador para visualización.
     * 
     * @param int $id - ID del reporte
     * @return \Illuminate\Http\Response - PDF para mostrar en navegador
     */
    public function mostrarPDFFalla($id)
    {
        try {
            $reporte = DB::table('tb_fallas')->where('id', $id)->first();
            if (!$reporte) {
                return response('Reporte no encontrado', 404)
                    ->header('Content-Type', 'text/plain');
            }

            $lugar = Lugar::find($reporte->id_lugar);
            $nombreLugar = $lugar ? $lugar->nombre : 'No especificado';

            $materials = [];
            if ($reporte->materials) {
                $materials = is_string($reporte->materials) ? json_decode($reporte->materials, true) : $reporte->materials;
                $materials = is_array($materials) ? $materials : [];
            }

            $data = [
                'id_reporte' => $reporte->id,
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

            return $pdf->stream('reporte_falla_' . $id . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error al mostrar PDF: ' . $e->getMessage());
            return response('Error al generar PDF: ' . $e->getMessage(), 500)
                ->header('Content-Type', 'text/plain');
        }
    }

    /**
     * ========================================================================
     * VERIFICAR CONTRASEÑA DEL USUARIO ACTUAL
     * ========================================================================
     * 
     * Valida la contraseña del usuario autenticado para
     * operaciones que requieren confirmación adicional.
     * 
     * @param Request $request - Contraseña a verificar
     * @return \Illuminate\Http\JsonResponse - Resultado de la verificación
     */
    public function verificarPassword(Request $request)
    {
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
    }

    /**
     * ========================================================================
     * VERIFICAR CONTRASEÑA DE USUARIO ESPECÍFICO
     * ========================================================================
     * 
     * Valida la contraseña de un usuario específico (generalmente admin)
     * para autorizar operaciones críticas como reportes de falla.
     * 
     * @param Request $request - ID del usuario y contraseña
     * @return \Illuminate\Http\JsonResponse - Resultado de la verificación
     */
    public function verificarPasswordUsuario(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
            'password' => 'required|string'
        ]);

        try {
            $usuario = Usuarios::find($request->usuario_id);

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no encontrado'
                ], 404);
            }

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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al verificar contraseña: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================================================
     * OBTENER LISTA DE USUARIOS ADMINISTRADORES
     * ========================================================================
     * 
     * Retorna todos los usuarios con permisos de administrador
     * para ser utilizados en selectores de autorización.
     * 
     * @return \Illuminate\Http\JsonResponse - Lista de administradores
     */
    public function getUsuariosAdmin()
    {
        try {
            $usuarios = Usuarios::where('tipo_usuario', 1)
                ->select('id_usuario as id', 'nombre as name', 'correo as email')
                ->get();

            return response()->json(['usuarios' => $usuarios], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar usuarios admin',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================================================
     * OBTENER USUARIOS POR UBICACIÓN ESPECÍFICA
     * ========================================================================
     * 
     * Retorna todos los usuarios asignados a una ubicación específica
     * para formularios de reportes y asignaciones.
     * 
     * @param int $id - ID de la ubicación
     * @return \Illuminate\Http\JsonResponse - Lista de usuarios de la ubicación
     */
    public function getUsuariosPorLugar($id)
    {
        try {
            $usuarios = Usuarios::where('id_lugar', $id)
                ->select('id_usuario as id', 'nombre as name', 'correo as email')
                ->get();

            return response()->json(['usuarios' => $usuarios], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar usuarios',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================================================
     * IMPORTACIÓN MASIVA DE KARDEX DESDE EXCEL
     * ========================================================================
     * 
     * Procesa archivos Excel con formato Kardex para importar
     * materiales masivamente con validación de estructura y datos.
     * 
     * @param Request $request - Archivo Excel y ubicación destino
     * @return \Illuminate\Http\JsonResponse - Resultado de la importación
     */
    public function importCardex(Request $request)
    {
        ini_set('max_execution_time', 300);
        $request->validate([
            'archivo_cardex' => 'required|file|mimes:xlsx,xls|max:10240',
            'id_lugar' => 'required|integer|exists:tb_lugares,id_lugar',
        ]);

        try {
            $archivo = $request->file('archivo_cardex');
            $spreadsheet = IOFactory::load($archivo->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $datos = $worksheet->toArray();

            $encabezadosRequeridos = [
                'Clave Material',
                'Descripción',
                'Genérico',
                'Clasificación',
                'Existencia',
                'Costo Promedio'
            ];

            $primeraFila = array_map('trim', $datos[0]);
            $encabezadosFaltantes = [];

            foreach ($encabezadosRequeridos as $encabezado) {
                if (!in_array($encabezado, $primeraFila)) {
                    $encabezadosFaltantes[] = $encabezado;
                }
            }

            if (!empty($encabezadosFaltantes)) {
                return response()->json([
                    'message' => 'Faltan columnas: ' . implode(', ', $encabezadosFaltantes)
                ], 422);
            }

            $indices = [];
            foreach ($encabezadosRequeridos as $encabezado) {
                $indices[$encabezado] = array_search($encabezado, $primeraFila);
            }

            $materialesImportados = 0;
            $errores = [];

            DB::beginTransaction();

            for ($i = 1; $i < count($datos); $i++) {
                $fila = $datos[$i];
                if (empty(array_filter($fila)))
                    continue;

                try {
                    $costo_raw = $fila[$indices['Costo Promedio']] ?? 0;
                    $costo_limpio = (float) preg_replace('/[^0-9.]/', '', $costo_raw);

                    $datosMaterial = [
                        'clave_material' => trim($fila[$indices['Clave Material']] ?? ''),
                        'descripcion' => trim($fila[$indices['Descripción']] ?? ''),
                        'generico' => trim($fila[$indices['Genérico']] ?? ''),
                        'clasificacion' => trim($fila[$indices['Clasificación']] ?? ''),
                        'existencia' => is_numeric($fila[$indices['Existencia']] ?? 0) ? (int) $fila[$indices['Existencia']] : 0,
                        'costo_promedio' => $costo_limpio,
                        'id_lugar' => $request->id_lugar,
                    ];

                    if (empty($datosMaterial['clave_material']) || empty($datosMaterial['descripcion'])) {
                        $errores[] = "Fila " . ($i + 1) . ": Clave Material y Descripción son obligatorios";
                        continue;
                    }

                    Material::updateOrCreate(
                        [
                            'clave_material' => $datosMaterial['clave_material'],
                            'id_lugar' => $datosMaterial['id_lugar']
                        ],
                        $datosMaterial
                    );

                    $materialesImportados++;
                } catch (\Exception $e) {
                    $errores[] = "Fila " . ($i + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            $mensaje = "Se importaron {$materialesImportados} materiales.";
            if (!empty($errores)) {
                $mensaje .= " Errores: " . implode('; ', array_slice($errores, 0, 5));
            }

            return response()->json(['message' => $mensaje]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error al procesar archivo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ========================================================================
     * DESCARGAR PLANTILLA DE KARDEX
     * ========================================================================
     * 
     * Genera y descarga una plantilla Excel con el formato
     * requerido para la importación de materiales.
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse - Archivo Excel
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $encabezados = ['Clave Material', 'Descripción', 'Genérico', 'Clasificación', 'Existencia', 'Costo Promedio'];
        $columna = 'A';
        foreach ($encabezados as $encabezado) {
            $sheet->setCellValue($columna . '1', $encabezado);
            $columna++;
        }

        $sheet->setCellValue('A2', 'MAT001');
        $sheet->setCellValue('B2', 'Material de ejemplo');
        $sheet->setCellValue('C2', 'Genérico ejemplo');
        $sheet->setCellValue('D2', 'Clasificación ejemplo');
        $sheet->setCellValue('E2', '100');
        $sheet->setCellValue('F2', '25.50');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = 'plantilla_cardex.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    /**
     * ========================================================================
     * EXPORTAR MATERIALES CON EXISTENCIA A EXCEL
     * ========================================================================
     * 
     * Genera un archivo Excel con todos los materiales que tienen
     * existencia mayor a cero, incluyendo cálculos de valor total.
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse - Archivo Excel
     */
    public function export()
    {
        $materiales = Material::where('existencia', '>', 0)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['ID Material', 'Clave Material', 'Descripción', 'Genérico', 'Clasificación', 'Existencia', 'Costo Promedio', 'Total ($)']
        ]);

        foreach ($materiales as $index => $material) {
            $sheet->fromArray([
                $material->id_material,
                $material->clave_material,
                $material->descripcion,
                $material->generico,
                $material->clasificacion,
                $material->existencia,
                $material->costo_promedio,
                $material->existencia * $material->costo_promedio,
            ], null, 'A' . ($index + 2));
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'materiales_existencia_' . now()->format('Y-m-d_H-i') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}