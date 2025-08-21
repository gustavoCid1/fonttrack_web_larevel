<?php

/**
 * Controlador Lugar - Gestión de ubicaciones geográficas
 * 
 * Maneja las operaciones CRUD para las ubicaciones del sistema.
 * Incluye validaciones y respuestas JSON para integración con AJAX.
 * Es la base del sistema de filtros geográficos para usuarios y materiales.
 * 
 * @author Jesús Felipe Avilez
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lugar;

class LugarController extends Controller
{
    /**
     * Mostrar listado completo de lugares
     */
    public function index()
    {
        $lugares = Lugar::all(); // Obtener todos los lugares de la base de datos
        return view('lugares', compact('lugares')); // Pasar la variable a la vista
    }

    /**
     * Mostrar formulario para crear nuevo lugar
     */
    public function create()
    {
        return view('lugares'); // Asegúrate de tener esta vista en `resources/views/lugares/create.blade.php`
    }

    /**
     * Almacenar un nuevo lugar en la base de datos
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'nullable|string|max:255'
        ]);

        $lugar = Lugar::create($request->all());

        return response()->json(['message' => 'Lugar creado correctamente', 'data' => $lugar], 201);
    }

    /**
     * Mostrar los datos de un lugar específico
     */
    public function show($id_lugar)
    {
        $lugar = Lugar::findOrFail($id_lugar);
        return response()->json(['data' => $lugar]);
    }

    /**
     * Obtener lugar para edición
     */
    public function edit($id_lugar)
    {
        $lugar = Lugar::findOrFail($id_lugar);
        return response()->json(['data' => $lugar]);
    }

    /**
     * Actualizar lugar existente
     */
    public function update(Request $request, $id_lugar)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'nullable|string|max:255'
        ]);

        $lugar = Lugar::findOrFail($id_lugar);
        $lugar->update($request->all());

        return response()->json(['message' => 'Lugar actualizado correctamente', 'data' => $lugar]);
    }

    /**
     * Eliminar lugar del sistema
     */
    public function destroy($id_lugar)
    {
        $lugar = Lugar::findOrFail($id_lugar);
        $lugar->delete();

        return response()->json(['message' => 'Lugar eliminado correctamente']);
    }
}