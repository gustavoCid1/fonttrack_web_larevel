<?php

/**
 * Controlador de Usuarios - Gestión completa de usuarios del sistema
 * 
 * Maneja todas las operaciones CRUD de usuarios, autenticación,
 * gestión de fotografías de perfil y control de acceso por tipo de usuario.
 * Incluye funcionalidades para administradores y usuarios normales.
 * 
 * @author Daniela Pérez Peralta
 * @author Jesús Felipe Avilez
 * @version 2.0.0
 */

namespace App\Http\Controllers;

use App\Models\Usuarios;
use App\Models\Lugar;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Muestra el listado paginado de usuarios con sus lugares
     */
    public function index()
    {
        $usuarios = Usuarios::with('lugar')->paginate(10);
        $lugares = Lugar::all();
        return view('index', compact('usuarios', 'lugares'));
    }

    /**
     * Vista de materiales para administradores con búsqueda
     */
    public function indexMateriales(Request $request)
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

    /**
     * Crea un nuevo usuario con validaciones y manejo de foto
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:tb_users,correo|max:255',
            'password' => 'required|min:6',
            'tipo_usuario' => 'required|integer',
            'foto_usuario' => 'nullable|image|mimes:jpeg,png|max:2048',
            'id_lugar' => 'required|integer|exists:tb_lugares,id_lugar',
        ]);

        // Manejo de foto de perfil
        if ($request->hasFile('foto_usuario')) {
            $archivo = $request->file('foto_usuario');
            $nombreFoto = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('img'), $nombreFoto);
            $validated['foto_usuario'] = $nombreFoto;
        } else {
            // Imagen predeterminada para nuevos usuarios
            $validated['foto_usuario'] = 'Sin_Foto.png';
        }

        $validated['password'] = Hash::make($validated['password']);

        $usuario = Usuarios::create($validated);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'data' => $usuario
        ], 201);
    }

    /**
     * Actualiza un usuario existente
     * Maneja cambio de foto y preserva la actual si no se sube nueva
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuarios::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'correo' => 'sometimes|required|email|unique:tb_users,correo,' . $id . ',id_usuario|max:255',
            'password' => 'nullable|min:6',
            'tipo_usuario' => 'sometimes|required|integer',
            'foto_usuario' => 'nullable|image|mimes:jpeg,png|max:2048',
            'id_lugar' => 'sometimes|required|integer|exists:tb_lugares,id_lugar',
        ]);

        // Manejo de foto de perfil en actualización
        if ($request->hasFile('foto_usuario')) {
            // Eliminar foto anterior si existe y no es la predeterminada
            if (
                $usuario->foto_usuario && $usuario->foto_usuario !== 'Sin_Foto.png' &&
                file_exists(public_path('img/' . $usuario->foto_usuario))
            ) {
                unlink(public_path('img/' . $usuario->foto_usuario));
            }
            $archivo = $request->file('foto_usuario');
            $nombreFoto = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('img'), $nombreFoto);
            $validated['foto_usuario'] = $nombreFoto;
        } else {
            // Mantener foto actual o asignar predeterminada si no tiene
            if (!$usuario->foto_usuario) {
                $validated['foto_usuario'] = 'Sin_Foto.png';
            }
        }

        // Encriptar contraseña solo si se proporciona
        if (isset($validated['password']) && !empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $usuario->update($validated);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data' => $usuario
        ]);
    }

    /**
     * Muestra los datos de un usuario específico
     */
    public function show($id)
    {
        $usuario = Usuarios::with('lugar')->find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return response()->json(['data' => $usuario]);
    }

    /**
     * Obtiene los datos de un usuario para edición
     */
    public function edit($id)
    {
        $usuario = Usuarios::with('lugar')->find($id);
        $lugares = Lugar::all();
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return response()->json(['data' => $usuario, 'lugares' => $lugares]);
    }

    /**
     * Elimina un usuario y su foto asociada
     */
    public function destroy($id)
    {
        $usuario = Usuarios::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Eliminar foto si existe y no es la predeterminada
        if (
            $usuario->foto_usuario && $usuario->foto_usuario !== 'Sin_Foto.png' &&
            file_exists(public_path('img/' . $usuario->foto_usuario))
        ) {
            unlink(public_path('img/' . $usuario->foto_usuario));
        }

        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    /**
     * Maneja el proceso de autenticación
     * Valida dominio de correo y redirige según tipo de usuario
     */
    public function login(Request $request)
    {
        $credentials = $request->only('correo', 'password');

        // Validar dominio corporativo
        if (!str_ends_with(strtolower(trim($credentials['correo'])), '@bonafont.com') && !str_ends_with(strtolower(trim($credentials['correo'])), '@danone.com')) {
            return back()->withErrors([
                'correo' => 'Solo se admiten correos con el dominio @bonafont.com o @danone.com',
            ]);
        }

        $usuario = Usuarios::where('correo', $credentials['correo'])->first();

        if (!$usuario) {
            return back()->withErrors([
                'correo' => 'El correo electrónico no está registrado.',
            ]);
        }

        if (!Hash::check($credentials['password'], $usuario->password)) {
            return back()->withErrors([
                'password' => 'La contraseña es incorrecta.',
            ]);
        }

        Auth::login($usuario);

        // Redirección según tipo de usuario
        if ($usuario->tipo_usuario == 1) {
            // Admin - vista completa
            return redirect()->action([UsuarioController::class, 'index']);
        } else {
            // Usuario normal - vista de materiales
            return redirect()->route('materiales.index');
        }
    }

    /**
     * Cierra sesión del usuario actual
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // === MÉTODOS PARA MODALES AJAX ===
    // Utilizan la misma lógica que los métodos principales

    public function modalShowUser($id)
    {
        return $this->show($id);
    }

    public function modalEditUser($id)
    {
        return $this->edit($id);
    }

    public function modalStoreUser(Request $request)
    {
        return $this->store($request);
    }

    public function modalUpdateUser(Request $request, $id)
    {
        return $this->update($request, $id);
    }

    public function modalDeleteUser($id)
    {
        return $this->destroy($id);
    }
}