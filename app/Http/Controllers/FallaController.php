<?php

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

class FallaController extends Controller
{
    public function index(Request $request)
    {
        $lugares = Lugar::all();
        $materiales = Material::all();
        $query = Falla::query();

        if ($request->has('id_lugar') && $request->id_lugar !== '') {
            $query->where('id_lugar', $request->id_lugar);
        }

        $fallas = $query->with('lugar')->paginate(10);

        return view('reportes_index', compact('fallas', 'lugares', 'materiales'));
    }

    public function show($id)
    {
        $falla = Falla::with(['lugar', 'materiales'])->findOrFail($id);
        $materials = $falla->materiales->map(function ($material) {
            return [
                'id' => $material->id_material,
                'descripcion' => $material->pivot->descripcion,
                'cantidad' => $material->pivot->cantidad,
            ];
        });

        return response()->json([
            'data' => [
                'id_falla' => $falla->id_falla,
                'id_lugar' => $falla->id_lugar,
                'usuario_reporta_id' => $falla->usuario_reporta_id,
                'nombre_usuario_reporta' => $falla->nombre_usuario_reporta,
                'correo_usuario_reporta' => $falla->correo_usuario_reporta,
                'eco' => $falla->eco,
                'placas' => $falla->placas,
                'marca' => $falla->marca,
                'anio' => $falla->anio,
                'km' => $falla->km,
                'fecha' => $falla->fecha,
                'nombre_conductor' => $falla->nombre_conductor,
                'descripcion' => $falla->descripcion,
                'observaciones' => $falla->observaciones,
                'autorizado_por' => $falla->autorizado_por,
                'correo_destino' => $falla->correo_destino,
            ],
            'materials' => $materials
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $validated = $request->validate([
            'id_lugar' => 'required|exists:tb_lugares,id_lugar',
            'usuario_reporta_id' => 'required|exists:tb_users,id_usuario',
            'nombre_usuario_reporta' => 'required|string|max:255',
            'correo_usuario_reporta' => 'required|email|max:255',
            'eco' => 'nullable|string|max:50',
            'placas' => 'nullable|string|max:50',
            'marca' => 'nullable|string|max:50',
            'anio' => 'nullable|string|max:4',
            'km' => 'nullable|string|max:20',
            'fecha' => 'nullable|date',
            'nombre_conductor' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'correo_destino' => 'nullable|email|max:255',
            'materials' => 'required|json',
            'nombre_usuario_revisa' => 'required|string|max:255',
            'correo_usuario_revisa' => 'required|email|max:255',
            'reviso_por' => 'required|string',
        ], [
            'id_lugar.required' => 'El lugar es requerido.',
            'usuario_reporta_id.required' => 'El usuario que reporta es requerido.',
            'nombre_usuario_reporta.required' => 'El nombre del usuario que reporta es requerido.',
            'correo_usuario_reporta.required' => 'El correo del usuario que reporta es requerido.',
            'nombre_usuario_revisa.required' => 'El nombre del usuario que revisa es requerido.',
            'correo_usuario_revisa.required' => 'El correo del usuario que revisa es requerido.',
            'reviso_por.required' => 'La contraseña de verificación es requerida.',
            'materials.required' => 'Debe incluir al menos un material.',
        ]);

        if (!Hash::check($request->reviso_por, Auth::user()->password)) {
            return response()->json([
                'errors' => ['reviso_por' => ['La contraseña de verificación es incorrecta.']]
            ], 422);
        }

        if ($request->correo_usuario_revisa !== Auth::user()->email) {
            return response()->json([
                'errors' => ['correo_usuario_revisa' => ['El correo del revisor debe corresponder al usuario autenticado.']]
            ], 422);
        }

        $materials = json_decode($request->materials, true);

        if (empty($materials)) {
            return response()->json(['errors' => ['materials' => ['Debe incluir al menos un material']]], 422);
        }

        foreach ($materials as $material) {
            if (!isset($material['id'], $material['cantidad']) || !is_numeric($material['cantidad']) || $material['cantidad'] < 1) {
                return response()->json(['errors' => ['materials' => ['Formato de materiales inválido']]], 422);
            }
        }

        try {
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
                'autorizado_por' => $request->nombre_usuario_revisa,
                'correo_destino' => $request->correo_destino,
            ]);

            foreach ($materials as $material) {
                $falla->materiales()->attach($material['id'], [
                    'descripcion' => $material['descripcion'],
                    'cantidad' => $material['cantidad'],
                ]);

                $materialModel = Material::find($material['id']);
                if ($materialModel) {
                    $materialModel->existencia -= $material['cantidad'];
                    $materialModel->save();
                }
            }

            Log::info('Reporte de falla creado exitosamente', [
                'falla_id' => $falla->id_falla,
                'usuario_reporta' => $request->nombre_usuario_reporta,
                'usuario_revisa' => $request->nombre_usuario_revisa,
                'lugar' => $request->id_lugar
            ]);

            return response()->json([
                'message' => 'Reporte creado exitosamente',
                'data' => ['id_falla' => $falla->id_falla]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear reporte de falla', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error interno del servidor al crear el reporte',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function enviar($id, Request $request)
    {
        $request->validate([
            'correo_destino' => 'required|email'
        ]);

        try {
            $falla = Falla::with(['lugar', 'materiales'])->findOrFail($id);
            $pdf = Pdf::loadView('reporte_fallo', ['falla' => $falla]);

            Mail::to($request->correo_destino)
                ->cc(Auth::user()->email)
                ->send(new ReporteFalla($falla, $pdf->output()));

            Log::info('Correo de reporte enviado exitosamente', [
                'falla_id' => $id,
                'correo_destino' => $request->correo_destino
            ]);

            return response()->json(['message' => 'Correo enviado exitosamente']);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de reporte', [
                'falla_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pdf($id)
    {
        try {
            $falla = Falla::with(['lugar', 'materiales'])->findOrFail($id);
            $pdf = Pdf::loadView('reporte_fallo', ['falla' => $falla]);
            return $pdf->stream('reporte_falla_' . $id . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error al generar PDF', [
                'falla_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Error al generar PDF'], 500);
        }
    }

    public function verificarPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'error' => 'Usuario no autenticado'], 401);
        }

        if (Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'success' => true,
                'user' => [
                    'nombre' => Auth::user()->nombre,
                    'email' => Auth::user()->email
                ]
            ]);
        }

        return response()->json(['success' => false, 'error' => 'Contraseña incorrecta'], 401);
    }

    public function usuariosPorLugar($id_lugar)
    {
        try {
            $usuarios = Usuarios::where('id_lugar', $id_lugar)
                               ->select('id_usuario as id', 'nombre as name', 'correo as email')
                               ->get();
            
            Log::info("Usuarios cargados para lugar ID: {$id_lugar}", [
                'count' => $usuarios->count(),
                'usuarios' => $usuarios->toArray()
            ]);

            return response()->json([
                'usuarios' => $usuarios
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al cargar usuarios para lugar ID: {$id_lugar}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al cargar usuarios',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsuariosPorLugar($id)
    {
        return $this->usuariosPorLugar($id);
    }

public function searchMaterials(Request $request)
{
    try {
        $query = $request->input('query', '');
        $materiales = Material::where('clave_material', 'LIKE', "%{$query}%")
                             ->orWhere('descripcion', 'LIKE', "%{$query}%")
                             ->select('id_material', 'clave_material', 'descripcion')
                             ->limit(50) // Limitar resultados
                             ->get();

        Log::info("Materiales buscados con query: {$query}", [
            'count' => $materiales->count()
        ]);

        return response()->json([
            'materiales' => $materiales
        ], 200);

    } catch (\Exception $e) {
        Log::error("Error al buscar materiales", [
            'query' => $request->input('query', ''),
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'error' => 'Error al buscar materiales',
            'message' => $e->getMessage()
        ], 500);
    }
}
}