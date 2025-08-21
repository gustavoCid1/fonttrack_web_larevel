<?php

/**
 * Archivo de rutas web de la aplicación
 * 
 * Este archivo contiene todas las rutas web de la aplicación Laravel para:
 * - Sistema de autenticación y sesiones
 * - Gestión de usuarios (CRUD completo)
 * - Gestión de materiales (vista administrativa y de usuario)
 * - Gestión de lugares/ubicaciones
 * - Sistema de reportes de fallas
 * - Sistema de notificaciones
 * - Gestión de vehículos
 * 
 * @package App\Routes
 * @version 2.0.0
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialUsuarioController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\FallaController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\VehiculoController;
use App\Models\Lugar;

/**
 * RUTA DE INICIO
 * Muestra la página de bienvenida de la aplicación
 */
Route::get('/', function () {
   return view('welcome');
});

/**
 * SISTEMA DE AUTENTICACIÓN Y GESTIÓN DE SESIONES
 * 
 * Rutas para el manejo de login, logout y autenticación de usuarios
 */

/**
 * Muestra el formulario de login con lista de lugares disponibles
 */
Route::get('/login', function () {
   $lugares = Lugar::all();
   return view('login', compact('lugares'));
})->name('login.view');

/**
 * Procesa el login del usuario
 */
Route::post('/login', [UsuarioController::class, 'login'])->name('login');

/**
 * Procesa el logout del usuario
 */
Route::post('/logout', [UsuarioController::class, 'logout'])->name('logout');

/**
 * DASHBOARD ADMINISTRATIVO
 * 
 * Acceso restringido solo para usuarios con rol de administrador
 */
Route::middleware(['auth', 'role:admin'])->group(function () {
   /**
    * Panel principal de administración
    */
   Route::get('/dashboard', function () {
      return view('dashboard');
   })->name('dashboard');
});

/**
 * GESTIÓN DE USUARIOS - OPERACIONES CRUD
 * 
 * Rutas para la gestión completa de usuarios del sistema
 */

/**
 * Lista todos los usuarios del sistema
 */
Route::get('/users', [UsuarioController::class, 'index'])->name('users');

/**
 * Muestra los detalles de un usuario específico
 */
Route::get('/users/{id}', [UsuarioController::class, 'show'])->name('user_detail');

/**
 * Registra un nuevo usuario en el sistema
 */
Route::post('/register_user', [UsuarioController::class, 'store'])->name('register_user');

/**
 * Muestra el formulario de edición de usuario
 */
Route::get('/edit_user/{id}', [UsuarioController::class, 'edit'])->name('edit_user');

/**
 * Actualiza los datos de un usuario existente
 */
Route::put('/update_user/{id}', [UsuarioController::class, 'update'])->name('update_user');

/**
 * Elimina un usuario del sistema
 */
Route::delete('/delete_user/{id}', [UsuarioController::class, 'destroy'])->name('delete_user');

/**
 * GESTIÓN DE USUARIOS VÍA MODAL (AJAX)
 * 
 * Rutas para operaciones de usuarios que se ejecutan mediante modales y AJAX
 */
Route::prefix('modal')->group(function () {
   /**
    * Obtiene los datos de un usuario para mostrar en modal
    */
   Route::get('/user/{id}', [UsuarioController::class, 'show'])->name('modal_show_user');
   
   /**
    * Obtiene los datos de un usuario para editar en modal
    */
   Route::get('/edit_user/{id}', [UsuarioController::class, 'edit'])->name('modal_edit_user');
   
   /**
    * Registra un nuevo usuario mediante modal
    */
   Route::post('/register_user', [UsuarioController::class, 'store'])->name('modal_register_user');
   
   /**
    * Actualiza un usuario mediante modal
    */
   Route::put('/update_user/{id}', [UsuarioController::class, 'update'])->name('modal_update_user');
   
   /**
    * Elimina un usuario mediante modal
    */
   Route::delete('/delete_user/{id}', [UsuarioController::class, 'destroy'])->name('modal_delete_user');
});

/**
 * GESTIÓN DE MATERIALES - VISTA ADMINISTRATIVA
 * 
 * Rutas para la administración completa del inventario de materiales
 */
Route::prefix('materials')->name('materials.')->group(function () {
   /**
    * Lista todos los materiales del inventario
    */
   Route::get('/', [MaterialController::class, 'index'])->name('index');
   
   /**
    * Exporta la lista de materiales a Excel
    */
   Route::get('/export', [MaterialController::class, 'export'])->name('export');
   
   /**
    * Descarga plantilla para importación de materiales
    */
   Route::get('/template', [MaterialController::class, 'downloadTemplate'])->name('template');
   
   /**
    * Crea un nuevo material en el inventario
    */
   Route::post('/', [MaterialController::class, 'store'])->name('store');
   
   /**
    * Muestra los detalles de un material específico
    */
   Route::get('/{id}', [MaterialController::class, 'show'])->name('show');
   
   /**
    * Muestra el formulario de edición de material
    */
   Route::get('/{id}/edit', [MaterialController::class, 'edit'])->name('edit');
   
   /**
    * Actualiza los datos de un material
    */
   Route::put('/{id}', [MaterialController::class, 'update'])->name('update');
   
   /**
    * Elimina un material del inventario
    */
   Route::delete('/{id}', [MaterialController::class, 'destroy'])->name('destroy');
   
   /**
    * Aumenta la existencia de un material
    */
   Route::post('/{id}/aumentar', [MaterialController::class, 'aumentarExistencia'])->name('aumentar');
   
   /**
    * Importa materiales desde archivo Excel
    */
   Route::post('/import', [MaterialController::class, 'importCardex'])->name('import');

   /**
    * REPORTES DE FALLA DE MATERIALES
    */
   
   /**
    * Crea un nuevo reporte de falla
    */
   Route::post('/reporte-falla', [MaterialController::class, 'crearReporteFalla'])->name('reporte-falla');
   
   /**
    * Muestra el PDF del reporte de falla
    */
   Route::get('/pdf/falla/{id}', [MaterialController::class, 'mostrarPDFFalla'])->name('pdf.falla');
   
   /**
    * GESTIÓN DE VEHÍCULOS POR UBICACIÓN
    */
   
   /**
    * Obtiene los vehículos de un lugar específico
    */
   Route::get('/vehiculos-lugar/{id_lugar}', [MaterialController::class, 'getVehiculosPorLugar'])->name('vehiculos.lugar');
   
   /**
    * Obtiene los datos de un vehículo por su número económico
    */
   Route::get('/vehiculo-eco/{eco}', [MaterialController::class, 'getVehiculoPorEco'])->name('vehiculo.eco');
});

/**
 * RUTAS DE COMPATIBILIDAD PARA MATERIALES
 * Mantienen la compatibilidad con versiones anteriores del sistema
 */
Route::get('/materials', [MaterialController::class, 'index'])->name('materials');
Route::get('/edit_material/{id}', [MaterialController::class, 'edit']);

/**
 * GESTIÓN DE MATERIALES - VISTA DE USUARIO FINAL
 * 
 * Rutas para usuarios normales que consultan el inventario
 */
Route::middleware(['auth'])->group(function () {
   /**
    * Lista de materiales para usuarios normales
    */
   Route::get('/materiales', [MaterialUsuarioController::class, 'index'])->name('materiales.index');
   
   /**
    * Búsqueda de materiales
    */
   Route::get('/materiales/search', [MaterialUsuarioController::class, 'searchMaterials'])->name('materiales.search');
   
   /**
    * Exporta materiales para usuarios normales
    */
   Route::get('/materiales/export', [MaterialUsuarioController::class, 'export'])->name('materiales.export');
   
   /**
    * Muestra detalles de un material específico
    */
   Route::get('/materiales/{id}', [MaterialUsuarioController::class, 'show'])->name('materiales.show');

   /**
    * GESTIÓN DEL PERFIL DE USUARIO
    */
   
   /**
    * Actualiza el nombre del usuario
    */
   Route::post('/usuario/update-name', [MaterialUsuarioController::class, 'updateName'])->name('usuario.update-name');
   
   /**
    * Actualiza la contraseña del usuario
    */
   Route::post('/usuario/update-password', [MaterialUsuarioController::class, 'updatePassword'])->name('usuario.update-password');
   
   /**
    * Actualiza la foto de perfil del usuario
    */
   Route::post('/usuario/update-photo', [MaterialUsuarioController::class, 'updatePhoto'])->name('usuario.update-photo');
});

/**
 * GESTIÓN DE LUGARES/UBICACIONES - OPERACIONES CRUD
 * 
 * Rutas para la administración de lugares y ubicaciones del sistema
 */
Route::prefix('lugares')->name('lugares.')->group(function () {
   /**
    * Lista todos los lugares registrados
    */
   Route::get('/', [LugarController::class, 'index'])->name('index');
   
   /**
    * Muestra el formulario para crear un nuevo lugar
    */
   Route::get('/create', [LugarController::class, 'create'])->name('create');
   
   /**
    * Guarda un nuevo lugar en la base de datos
    */
   Route::post('/', [LugarController::class, 'store'])->name('store');
   
   /**
    * Muestra los detalles de un lugar específico
    */
   Route::get('/{id_lugar}', [LugarController::class, 'show'])->name('show');
   
   /**
    * Muestra el formulario de edición de lugar
    */
   Route::get('/{id_lugar}/edit', [LugarController::class, 'edit'])->name('edit');
   
   /**
    * Actualiza los datos de un lugar
    */
   Route::put('/{id_lugar}', [LugarController::class, 'update'])->name('update');
   
   /**
    * Elimina un lugar del sistema
    */
   Route::delete('/{id_lugar}', [LugarController::class, 'destroy'])->name('destroy');
   
   /**
    * Obtiene la lista de usuarios asignados a un lugar específico
    */
   Route::get('/{id}/usuarios', [MaterialUsuarioController::class, 'getUsersByPlace'])
        ->middleware('auth')
        ->name('usuarios');
});

/**
 * SISTEMA DE REPORTES DE FALLOS CEDIS
 * 
 * Rutas para el manejo de reportes de fallas del sistema CEDIS
 */

/**
 * Muestra el formulario de reporte de fallo
 */
Route::get('/reporte', function () {
   return view('reporte_fallo');
})->name('reporte.form');

/**
 * Procesa el envío del reporte de fallo
 */
Route::post('/reporte', [ReporteController::class, 'enviar'])->name('reporte.enviar');

/**
 * SISTEMA COMPLETO DE GESTIÓN DE FALLAS
 * 
 * Rutas para la gestión integral del sistema de fallas
 */
Route::prefix('fallas')->name('fallas.')->group(function () {
   /**
    * Lista todas las fallas registradas
    */
   Route::get('/', [FallaController::class, 'index'])->name('index');
   
   /**
    * Muestra los detalles de una falla específica
    */
   Route::get('/{id}', [FallaController::class, 'show'])->name('show');
   
   /**
    * Crea una nueva falla en el sistema
    */
   Route::post('/', [FallaController::class, 'store'])->name('store');
   
   /**
    * Envía una falla para su procesamiento
    */
   Route::post('/enviar/{id}', [FallaController::class, 'enviar'])->name('enviar');
   
   /**
    * Genera el PDF de una falla específica
    */
   Route::get('/pdf/{id}', [FallaController::class, 'pdf'])->name('pdf');
   
   /**
    * Obtiene todos los materiales disponibles para reportes de falla
    */
   Route::get('/materiales', [FallaController::class, 'getAllMaterials'])->name('materiales');
   
   /**
    * Búsqueda de materiales para reportes de falla
    */
   Route::get('/search-materials', [FallaController::class, 'searchMaterials'])->name('search-materials');
});

/**
 * RUTAS ESPECIALES Y FUNCIONES AUXILIARES
 * 
 * Rutas para funcionalidades específicas del sistema
 */

/**
 * Muestra la vista de reportes generales
 */
Route::get('/reportes', [FallaController::class, 'showReportes'])->name('reportes.index');

/**
 * Verifica la contraseña del usuario actual
 */
Route::post('/verificar-password', [FallaController::class, 'verificarPassword'])->name('verificar-password');

/**
 * Obtiene la lista de usuarios administradores
 */
Route::get('/usuarios/admin', [FallaController::class, 'getUsuariosAdmin']);

/**
 * Verifica la contraseña de un usuario específico
 */
Route::post('/verificar-password-usuario', [FallaController::class, 'verificarPasswordUsuario']);

/**
 * SISTEMA DE NOTIFICACIONES
 * 
 * Rutas para el manejo completo del sistema de notificaciones
 */
Route::middleware(['auth'])->group(function () {
   /**
    * Crea una nueva notificación
    */
   Route::post('/notificaciones', [NotificacionController::class, 'store'])->name('notificaciones.store');
   
   /**
    * Obtiene las notificaciones pendientes del usuario
    */
   Route::get('/notificaciones/pendientes', [NotificacionController::class, 'getPendientes'])->name('notificaciones.pendientes');
   
   /**
    * Obtiene el contador de notificaciones pendientes
    */
   Route::get('/notificaciones/contador', [NotificacionController::class, 'getContador'])->name('notificaciones.contador');
   
   /**
    * Muestra los detalles de una notificación específica
    */
   Route::get('/notificaciones/{id}', [NotificacionController::class, 'show'])->name('notificaciones.show');
   
   /**
    * Aprueba una notificación pendiente
    */
   Route::post('/notificaciones/{id}/aprobar', [NotificacionController::class, 'aprobar'])->name('notificaciones.aprobar');
   
   /**
    * Rechaza una notificación pendiente
    */
   Route::post('/notificaciones/{id}/rechazar', [NotificacionController::class, 'rechazar'])->name('notificaciones.rechazar');
   
   /**
    * Obtiene la lista de notificaciones rechazadas
    */
   Route::get('/notificaciones/rechazadas/lista', [NotificacionController::class, 'getRechazadas'])->name('notificaciones.rechazadas');
   
   /**
    * Limpia las notificaciones rechazadas del sistema
    */
   Route::delete('/notificaciones/rechazadas/limpiar', [NotificacionController::class, 'limpiarRechazadas'])->name('notificaciones.limpiar');
});

/**
 * GESTIÓN DE VEHÍCULOS
 * 
 * Rutas para la administración completa del parque vehicular
 * Nota: Se definen las rutas específicas antes del resource para evitar conflictos
 */
Route::middleware(['auth'])->group(function () {
   /**
    * Búsqueda de vehículos en el sistema
    */
   Route::get('/vehiculos/search', [VehiculoController::class, 'search'])->name('vehiculos.search');
   
   /**
    * Importa vehículos desde archivo Excel
    */
   Route::post('/vehiculos/import-excel', [VehiculoController::class, 'importExcel'])->name('vehiculos.import-excel');
   
   /**
    * Obtiene los detalles completos de un vehículo por ID
    */
   Route::get('/vehiculos/{id}/details', [MaterialUsuarioController::class, 'getVehicleById'])->name('vehiculos.details');
   
   /**
    * Resource controller para operaciones CRUD estándar de vehículos
    * Incluye: index, create, store, show, edit, update, destroy
    */
   Route::resource('vehiculos', VehiculoController::class);
});