<?php

/**
 * Controlador Vehiculo - Sistema completo de gestión de vehículos
 * 
 * Maneja el CRUD completo de vehículos con control de permisos por ubicación.
 * Incluye APIs REST para búsquedas AJAX, importación masiva desde Excel,
 * validaciones de seguridad y filtros automáticos por lugar del usuario.
 * Funciona como híbrido web/API para máxima flexibilidad de frontend.
 * 
 * @author Jesús Felipe Avilez
 * @author Daniela Pérez Peralta
 * @version 2.0.0
 */

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Lugar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class VehiculoController extends Controller
{
    /**
     * Mostrar listado de vehículos con filtros por lugar del usuario
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $lugares = Lugar::all();
            
            $vehiculosQuery = Vehiculo::with('lugar');
            
            // Filtrar por lugar del usuario si no es administrador
            if ($user->tipo_usuario != 1 && $user->id_lugar) {
                $vehiculosQuery->where('id_lugar', $user->id_lugar);
                Log::info('Usuario NO admin - Filtrando por lugar: ' . $user->id_lugar);
            } else {
                Log::info('Usuario admin - Puede ver todos los lugares');
            }
            
            // Búsqueda por query string
            if ($request->filled('query')) {
                $vehiculosQuery->where(function($q) use ($request) {
                    $query = $request->query;
                    $q->where('eco', 'like', '%' . $query . '%')
                      ->orWhere('placas', 'like', '%' . $query . '%')
                      ->orWhere('marca', 'like', '%' . $query . '%')
                      ->orWhere('modelo', 'like', '%' . $query . '%')
                      ->orWhere('conductor_habitual', 'like', '%' . $query . '%');
                });
            }
            
            // Filtrar por lugar (solo para admins)
            if ($request->filled('lugar') && $user->tipo_usuario == 1) {
                $vehiculosQuery->where('id_lugar', $request->lugar);
            }
            
            $vehiculos = $vehiculosQuery->orderBy('eco')->paginate(15);
            
            return view('vehiculos_index', compact('vehiculos', 'lugares'));
            
        } catch (\Exception $e) {
            Log::error('Error en VehiculoController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar vehículos');
        }
    }

    /**
     * Mostrar formulario para crear nuevo vehículo
     */
    public function create()
    {
        $lugares = Lugar::all();
        return view('vehiculos.create', compact('lugares'));
    }

    /**
     * Almacenar nuevo vehículo con validaciones de permisos
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_lugar' => 'required|exists:tb_lugares,id_lugar',
                'eco' => 'required|string|unique:tb_vehiculos,eco',
                'placas' => 'nullable|string|max:20',
                'marca' => 'nullable|string|max:50',
                'modelo' => 'nullable|string|max:50',
                'anio' => 'nullable|string|max:4',
                'kilometraje' => 'nullable|integer|min:0',
                'conductor_habitual' => 'nullable|string|max:100',
                'color' => 'nullable|string|max:30',
                'estatus' => 'required|in:activo,inactivo,mantenimiento'
            ]);

            // Verificar permisos para crear vehículo
            $user = Auth::user();
            if ($user->tipo_usuario != 1 && $user->id_lugar != $validated['id_lugar']) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permisos para crear vehículos en este lugar'
                    ], 403);
                }
                return redirect()->back()->withInput()->with('error', 'No tienes permisos para crear vehículos en este lugar');
            }

            $vehiculo = Vehiculo::create($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehículo registrado correctamente',
                    'data' => $vehiculo
                ], 201);
            }

            return redirect()->route('vehiculos.index')->with('success', 'Vehículo registrado correctamente');

        } catch (\Exception $e) {
            Log::error('Error al crear vehículo: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar vehículo: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withInput()->with('error', 'Error al registrar vehículo');
        }
    }

    /**
     * API/Web: Mostrar vehículo específico con validación de permisos
     */
    public function show($id)
    {
        try {
            $vehiculo = Vehiculo::with('lugar')->findOrFail($id);
            
            // Verificar permisos para ver vehículo
            $user = Auth::user();
            if ($user->tipo_usuario != 1 && $user->id_lugar != $vehiculo->id_lugar) {
                if (request()->expectsJson()) {
                    return response()->json(['error' => 'No autorizado'], 403);
                }
                return redirect()->route('vehiculos.index')->with('error', 'No tienes permisos para ver este vehículo');
            }

            if (request()->expectsJson()) {
                return response()->json(['data' => $vehiculo]);
            }

            return view('vehiculos.show', compact('vehiculo'));

        } catch (\Exception $e) {
            Log::error('Error al mostrar vehículo: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Vehículo no encontrado'], 404);
            }
            
            return redirect()->route('vehiculos.index')->with('error', 'Vehículo no encontrado');
        }
    }

    /**
     * API/Web: Obtener vehículo para edición
     */
    public function edit($id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);
            $lugares = Lugar::all();
            
            // Verificar permisos para editar vehículo
            $user = Auth::user();
            if ($user->tipo_usuario != 1 && $user->id_lugar != $vehiculo->id_lugar) {
                if (request()->expectsJson()) {
                    return response()->json(['error' => 'No autorizado'], 403);
                }
                return redirect()->route('vehiculos.index')->with('error', 'No tienes permisos para editar este vehículo');
            }

            if (request()->expectsJson()) {
                return response()->json(['data' => $vehiculo, 'lugares' => $lugares]);
            }

            return view('vehiculos.edit', compact('vehiculo', 'lugares'));

        } catch (\Exception $e) {
            Log::error('Error al editar vehículo: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Vehículo no encontrado'], 404);
            }
            
            return redirect()->route('vehiculos.index')->with('error', 'Vehículo no encontrado');
        }
    }

    /**
     * API/Web: Actualizar vehículo existente con validaciones
     */
    public function update(Request $request, $id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);
            
            // Verificar permisos para actualizar vehículo
            $user = Auth::user();
            if ($user->tipo_usuario != 1 && $user->id_lugar != $vehiculo->id_lugar) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'No autorizado'], 403);
                }
                return redirect()->route('vehiculos.index')->with('error', 'No tienes permisos para editar este vehículo');
            }

            $validated = $request->validate([
                'id_lugar' => 'required|exists:tb_lugares,id_lugar',
                'eco' => 'required|string|unique:tb_vehiculos,eco,' . $id,
                'placas' => 'nullable|string|max:20',
                'marca' => 'nullable|string|max:50',
                'modelo' => 'nullable|string|max:50',
                'anio' => 'nullable|string|max:4',
                'kilometraje' => 'nullable|integer|min:0',
                'conductor_habitual' => 'nullable|string|max:100',
                'color' => 'nullable|string|max:30',
                'estatus' => 'required|in:activo,inactivo,mantenimiento'
            ]);

            // Verificar que no cambie a un lugar no autorizado
            if ($user->tipo_usuario != 1 && $user->id_lugar != $validated['id_lugar']) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No puedes mover vehículos a otros lugares'
                    ], 403);
                }
                return redirect()->back()->withInput()->with('error', 'No puedes mover vehículos a otros lugares');
            }

            $vehiculo->update($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehículo actualizado correctamente',
                    'data' => $vehiculo
                ]);
            }

            return redirect()->route('vehiculos.index')->with('success', 'Vehículo actualizado correctamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar vehículo: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar vehículo: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withInput()->with('error', 'Error al actualizar vehículo');
        }
    }

    /**
     * API/Web: Eliminar vehículo con validación de permisos
     */
    public function destroy($id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);
            
            // Verificar permisos para eliminar vehículo
            $user = Auth::user();
            if ($user->tipo_usuario != 1 && $user->id_lugar != $vehiculo->id_lugar) {
                if (request()->expectsJson()) {
                    return response()->json(['error' => 'No autorizado'], 403);
                }
                return redirect()->route('vehiculos.index')->with('error', 'No tienes permisos para eliminar este vehículo');
            }

            $vehiculo->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehículo eliminado correctamente'
                ]);
            }

            return redirect()->route('vehiculos.index')->with('success', 'Vehículo eliminado correctamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar vehículo: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar vehículo'
                ], 500);
            }
            
            return redirect()->route('vehiculos.index')->with('error', 'Error al eliminar vehículo');
        }
    }

    /**
     * API: Búsqueda AJAX de vehículos con filtros automáticos
     */
    public function search(Request $request)
    {
        try {
            $query = trim($request->input('q', ''));
            
            Log::info('VehiculoController@search iniciado', [
                'query' => $query,
                'user_id' => Auth::id()
            ]);
            
            // Validar que hay un query válido
            if (empty($query)) {
                Log::info('Query vacío en vehicle search');
                return response()->json([]);
            }
            
            if (strlen($query) < 2) {
                Log::info('Query muy corto en vehicle search: ' . $query);
                return response()->json([]);
            }
            
            // Verificar autenticación
            if (!Auth::check()) {
                Log::error('Usuario no autenticado en vehicle search');
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }
            
            $user = Auth::user();
            $userLugar = $user->id_lugar ?? null;
            
            Log::info('Usuario en vehicle search', [
                'user_id' => $user->id_usuario ?? $user->id,
                'user_lugar' => $userLugar,
                'tipo_usuario' => $user->tipo_usuario
            ]);
            
            // Construir query base con relaciones
            $vehiculosQuery = Vehiculo::with('lugar');
            
            // Filtrar por lugar del usuario si no es administrador
            if ($user->tipo_usuario != 1 && $userLugar) {
                $vehiculosQuery->where('id_lugar', $userLugar);
                Log::info('Filtrando vehículos por lugar del usuario: ' . $userLugar);
            } else {
                Log::info('Usuario admin - buscando en todos los vehículos');
            }
            
            // Búsqueda en múltiples campos
            $vehiculos = $vehiculosQuery
                ->where(function($q) use ($query) {
                    $q->where('eco', 'like', '%' . $query . '%')
                      ->orWhere('placas', 'like', '%' . $query . '%')
                      ->orWhere('marca', 'like', '%' . $query . '%')
                      ->orWhere('modelo', 'like', '%' . $query . '%')
                      ->orWhere('conductor_habitual', 'like', '%' . $query . '%');
                })
                ->whereIn('estatus', ['activo', 'mantenimiento']) // Solo activos y en mantenimiento
                ->select([
                    'id', 
                    'eco', 
                    'placas', 
                    'marca', 
                    'modelo',
                    'anio',
                    'kilometraje',
                    'conductor_habitual',
                    'estatus',
                    'color',
                    'id_lugar'
                ])
                ->orderBy('eco')
                ->limit(20)
                ->get();

            Log::info('Resultados de vehicle search', [
                'query' => $query,
                'user_lugar' => $userLugar,
                'total_resultados' => $vehiculos->count(),
                'vehiculos_encontrados' => $vehiculos->pluck('eco')->toArray()
            ]);

            // Incluir información del lugar en la respuesta
            $vehiculos->load('lugar');

            return response()->json($vehiculos);
            
        } catch (\Exception $e) {
            Log::error('Error en búsqueda de vehículos', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'query' => $request->input('q'),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'error' => 'Error en la búsqueda de vehículos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Importar vehículos masivamente desde archivo Excel
     */
    public function importExcel(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
                'lugar_import' => 'required|exists:tb_lugares,id_lugar'
            ]);

            $file = $request->file('excel_file');
            $lugarId = $request->lugar_import;
            
            // Verificar permisos del usuario para importar
            $user = Auth::user();
            if ($user->tipo_usuario != 1) {
                // Si no es admin, solo puede importar a su lugar
                if ($user->id_lugar != $lugarId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permisos para importar vehículos a este lugar'
                    ], 403);
                }
            }

            // Cargar el archivo Excel
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            $importados = 0;
            $errores = [];
            $duplicados = 0;

            // Comenzar desde la fila 2 (asumiendo que la fila 1 tiene headers)
            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    // Leer los datos de cada columna
                    $eco = trim($worksheet->getCell('A' . $row)->getCalculatedValue() ?? '');
                    $placas = trim($worksheet->getCell('B' . $row)->getCalculatedValue() ?? '');
                    $marca = trim($worksheet->getCell('C' . $row)->getCalculatedValue() ?? '');
                    $modelo = trim($worksheet->getCell('D' . $row)->getCalculatedValue() ?? '');
                    $anio = trim($worksheet->getCell('E' . $row)->getCalculatedValue() ?? '');
                    $kilometraje = trim($worksheet->getCell('F' . $row)->getCalculatedValue() ?? '');
                    $color = trim($worksheet->getCell('G' . $row)->getCalculatedValue() ?? '');
                    $conductor = trim($worksheet->getCell('H' . $row)->getCalculatedValue() ?? '');
                    $estatus = trim($worksheet->getCell('I' . $row)->getCalculatedValue() ?? '');

                    // Saltar filas vacías
                    if (empty($eco)) {
                        continue;
                    }

                    // Verificar si el ECO ya existe
                    if (Vehiculo::where('eco', $eco)->exists()) {
                        $duplicados++;
                        $errores[] = "Fila {$row}: El ECO '{$eco}' ya existe";
                        continue;
                    }

                    // Validar y limpiar datos
                    $estatus = strtolower($estatus);
                    if (!in_array($estatus, ['activo', 'inactivo', 'mantenimiento'])) {
                        $estatus = 'activo'; // Valor por defecto
                    }

                    // Limpiar kilometraje (remover caracteres no numéricos)
                    $kilometraje = preg_replace('/[^0-9]/', '', $kilometraje);
                    $kilometraje = $kilometraje ? (int)$kilometraje : 0;

                    // Validar año
                    $anio = preg_replace('/[^0-9]/', '', $anio);
                    if (strlen($anio) > 4) {
                        $anio = substr($anio, 0, 4);
                    }

                    // Crear el vehículo
                    Vehiculo::create([
                        'id_lugar' => $lugarId,
                        'eco' => $eco,
                        'placas' => $placas ?: null,
                        'marca' => $marca ?: null,
                        'modelo' => $modelo ?: null,
                        'anio' => $anio ?: null,
                        'kilometraje' => $kilometraje,
                        'color' => $color ?: null,
                        'conductor_habitual' => $conductor ?: null,
                        'estatus' => $estatus
                    ]);

                    $importados++;

                } catch (\Exception $e) {
                    $errores[] = "Fila {$row}: " . $e->getMessage();
                    Log::error("Error importando fila {$row}: " . $e->getMessage());
                }
            }

            $mensaje = "Importación completada. {$importados} vehículos importados exitosamente.";
            
            if ($duplicados > 0) {
                $mensaje .= " {$duplicados} vehículos duplicados omitidos.";
            }
            
            if (!empty($errores)) {
                Log::warning('Errores durante importación de vehículos:', $errores);
                $mensaje .= " Se encontraron algunos errores (revisar logs).";
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'data' => [
                    'importados' => $importados,
                    'duplicados' => $duplicados,
                    'errores' => count($errores)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en importación de vehículos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al importar archivo: ' . $e->getMessage()
            ], 500);
        }
    }
}