<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Lugar;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    // ========== MÉTODOS PRINCIPALES ==========
    
    /**
     * Vista completa para administradores
     */
    public function index(Request $request)
    {
        $lugares = Lugar::all();
        $query = $request->input('query');

        $materiales = Material::when($query, function ($q) use ($query) {
            $q->where(function ($subquery) use ($query) {
                $subquery->where('clave_material', 'like', '%' . $query . '%')
                    ->orWhere('descripcion', 'like', '%' . $query . '%')
                    ->orWhere('generico', 'like', '%' . $query . '%')
                    ->orWhere('clasificacion', 'like', '%' . $query . '%')
                    ->orWhere('existencia', 'like', '%' . $query . '%')
                    ->orWhere('costo_promedio', 'like', '%' . $query . '%');
            });
        })->paginate(10);

        return view('index_materiales', compact('materiales', 'lugares'));
    }

    /**
     * Vista simplificada para usuarios
     */
    public function indexSimple(Request $request)
    {
        $lugares = Lugar::all();
        $query = $request->input('query');

        $materiales = Material::when($query, function ($q) use ($query) {
            $q->where(function ($subquery) use ($query) {
                $subquery->where('clave_material', 'like', '%' . $query . '%')
                    ->orWhere('descripcion', 'like', '%' . $query . '%')
                    ->orWhere('generico', 'like', '%' . $query . '%')
                    ->orWhere('clasificacion', 'like', '%' . $query . '%')
                    ->orWhere('existencia', 'like', '%' . $query . '%')
                    ->orWhere('costo_promedio', 'like', '%' . $query . '%');
            });
        })->paginate(10);

        return view('index_materiales_simple', compact('materiales', 'lugares'));
    }

    // ========== MÉTODOS CRUD COMPARTIDOS ==========
    
    public function store(Request $request)
    {
        $request->validate([
            'clave_material' => 'required',
            'descripcion' => 'required',
            'generico' => 'nullable',
            'clasificacion' => 'nullable',
            'existencia' => 'required|integer',
            'costo_promedio' => 'required|numeric',
            'id_lugar' => 'nullable|integer|exists:tb_lugares,id_lugar',
        ]);

        $material = Material::create($request->all());
        return response()->json(['message' => 'Material agregado', 'data' => $material], 201);
    }

    public function show($id)
    {
        if (!is_numeric($id)) {
            return abort(404);
        }

        $material = Material::find($id);
        if (!$material) {
            return response()->json(['error' => 'Material no encontrado'], 404);
        }
        return response()->json(['data' => $material]);
    }

    public function edit($id)
    {
        $material = Material::find($id);
        if (!$material) {
            return response()->json(['error' => 'Material no encontrado'], 404);
        }
        return response()->json(['data' => $material]);
    }

    public function update(Request $request, $id)
    {
        $material = Material::find($id);
        if (!$material) {
            return response()->json(['error' => 'Material no encontrado'], 404);
        }

        $request->validate([
            'clave_material' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|required|string|max:255',
            'generico' => 'nullable',
            'clasificacion' => 'nullable',
            'existencia' => 'required|integer',
            'costo_promedio' => 'required|numeric',
            'id_lugar' => 'nullable|integer|exists:tb_lugares,id_lugar',
        ]);

        $material->update($request->all());
        return response()->json(['message' => 'Material actualizado correctamente', 'data' => $material]);
    }

    public function destroy($id)
    {
        Material::findOrFail($id)->delete();
        return response()->json(['message' => 'Material eliminado']);
    }

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

    // ========== MÉTODOS DE IMPORTACIÓN/EXPORTACIÓN ==========
    
    public function importCardex(Request $request)
    {
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
                'Clave Material', 'Descripción', 'Genérico', 'Clasificación', 'Existencia', 'Costo Promedio'
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
                if (empty(array_filter($fila))) continue;

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