<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use App\Models\Lugar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // Listado de usuarios paginados (10 por página)
    public function index()
    {
        $usuarios = Usuarios::with('lugar')->paginate(10);
        $lugares = Lugar::all();
        return view('index', compact('usuarios', 'lugares'));
    }

    // Vista de materiales para administradores
 
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:255',
            'correo'       => 'required|email|unique:tb_users,correo|max:255',
            'password'     => 'required|min:6',
            'tipo_usuario' => 'required|integer',
            'foto_usuario' => 'nullable|image|mimes:jpeg,png|max:2048',
            'id_lugar'     => 'required|integer|exists:tb_lugares,id_lugar',
        ]);

        if ($request->hasFile('foto_usuario')) {
            $archivo = $request->file('foto_usuario');
            $nombreFoto = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('img'), $nombreFoto);
            $validated['foto_usuario'] = $nombreFoto;
        } else {
            // Asignar imagen predeterminada en creación
            $validated['foto_usuario'] = 'Sin_Foto.png';
        }

        $validated['password'] = Hash::make($validated['password']);

        $usuario = Usuarios::create($validated);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'data'    => $usuario
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuarios::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre'       => 'sometimes|required|string|max:255',
            'correo'       => 'sometimes|required|email|unique:tb_users,correo,' . $id . ',id_usuario|max:255',
            'password'     => 'nullable|min:6',
            'tipo_usuario' => 'sometimes|required|integer',
            'foto_usuario' => 'nullable|image|mimes:jpeg,png|max:2048',
            'id_lugar'     => 'sometimes|required|integer|exists:tb_lugares,id_lugar',
        ]);

        if ($request->hasFile('foto_usuario')) {
            // Si ya existe una foto y NO es la predeterminada, la eliminamos
            if ($usuario->foto_usuario && $usuario->foto_usuario !== 'Sin_Foto.png' &&
                file_exists(public_path('img/' . $usuario->foto_usuario))) {
                unlink(public_path('img/' . $usuario->foto_usuario));
            }
            $archivo = $request->file('foto_usuario');
            $nombreFoto = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('img'), $nombreFoto);
            $validated['foto_usuario'] = $nombreFoto;
        } else {
            // Si no se envía un nuevo archivo, mantenemos la foto actual.
            // Opcional: Si el usuario no tiene foto asignada, se establece la predeterminada.
            if (!$usuario->foto_usuario) {
                $validated['foto_usuario'] = 'Sin_Foto.png';
            }
        }

        if (isset($validated['password']) && !empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $usuario->update($validated);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data'    => $usuario
        ]);
    }

    public function show($id)
    {
        $usuario = Usuarios::with('lugar')->find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return response()->json(['data' => $usuario]);
    }

    public function edit($id)
    {
        $usuario = Usuarios::with('lugar')->find($id);
        $lugares = Lugar::all();
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return response()->json(['data' => $usuario, 'lugares' => $lugares]);
    }

    public function destroy($id)
    {
        $usuario = Usuarios::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        if ($usuario->foto_usuario && $usuario->foto_usuario !== 'Sin_Foto.png' &&
            file_exists(public_path('img/' . $usuario->foto_usuario))) {
            unlink(public_path('img/' . $usuario->foto_usuario));
        }

        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('correo', 'password');

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

        // Redirección según el tipo de usuario
        if ($usuario->tipo_usuario == 1) {
            // Usuario tipo 1 = Admin - va a la vista normal (como estaba antes)
            return redirect()->action([UsuarioController::class, 'index']);
        } else {
            // Usuario tipo 2 = Usuario normal - va a materiales simple
            return redirect()->route('materiales.index');
        }
    }


    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Funciones para uso vía MODAL (AJAX) utilizando la misma lógica
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