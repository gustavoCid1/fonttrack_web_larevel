<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\FallaController;
use App\Models\Lugar;

/* ====================
   PÁGINA DE INICIO
==================== */
Route::get('/', function () {
   return view('welcome');
});

/* ============================
   AUTENTICACIÓN Y SESIÓN
============================ */
Route::get('/login', function () {
   $lugares = Lugar::all();
   return view('login', compact('lugares'));
})->name('login.view');

Route::post('/login', [UsuarioController::class, 'login'])->name('login');
Route::post('/logout', [UsuarioController::class, 'logout'])->name('logout');

/* ============================
   DASHBOARD (solo admins)
============================ */
Route::middleware(['auth', 'role:admin'])->group(function () {
   Route::get('/dashboard', function () {
      return view('dashboard');
   })->name('dashboard');
});

/* ====================
   CRUD: USUARIOS
==================== */
Route::get('/users', [UsuarioController::class, 'index'])->name('users');
Route::get('/users/{id}', [UsuarioController::class, 'show'])->name('user_detail');
Route::post('/register_user', [UsuarioController::class, 'store'])->name('register_user');
Route::get('/edit_user/{id}', [UsuarioController::class, 'edit'])->name('edit_user');
Route::put('/update_user/{id}', [UsuarioController::class, 'update'])->name('update_user');
Route::delete('/delete_user/{id}', [UsuarioController::class, 'destroy'])->name('delete_user');

/* ===========================================
   CRUD vía MODAL (AJAX): USUARIOS
=========================================== */
Route::prefix('modal')->group(function () {
   Route::get('/user/{id}', [UsuarioController::class, 'show'])->name('modal_show_user');
   Route::get('/edit_user/{id}', [UsuarioController::class, 'edit'])->name('modal_edit_user');
   Route::post('/register_user', [UsuarioController::class, 'store'])->name('modal_register_user');
   Route::put('/update_user/{id}', [UsuarioController::class, 'update'])->name('modal_update_user');
   Route::delete('/delete_user/{id}', [UsuarioController::class, 'destroy'])->name('modal_delete_user');
});

/* ===============================================
   CRUD: MATERIALES (VISTA ADMIN COMPLETA)
=============================================== */
Route::get('/materials', [MaterialController::class, 'index'])->name('materials');
Route::get('/materials/export', [MaterialController::class, 'export'])->name('materials.export');
Route::post('/materials', [MaterialController::class, 'store']);
Route::get('/materials/{id}', [MaterialController::class, 'show']);
Route::get('/edit_material/{id}', [MaterialController::class, 'edit']);
Route::put('/materials/{id}', [MaterialController::class, 'update']);
Route::delete('/materials/{id}', [MaterialController::class, 'destroy']);
Route::post('/materials/{id}/aumentar', [MaterialController::class, 'aumentarExistencia']);
Route::post('/materials/import', [MaterialController::class, 'importCardex']);

/* ================================================
   CRUD: MATERIALES (VISTA SIMPLE PARA USUARIOS)
================================================ */
Route::get('/materiales', [MaterialController::class, 'indexSimple'])->name('materiales.index');
Route::get('/materiales/export', [MaterialController::class, 'export'])->name('materiales.export');
Route::post('/materiales', [MaterialController::class, 'store']); // Misma función
Route::get('/materiales/{id}', [MaterialController::class, 'show']); // Misma función
Route::get('/materiales/{id}/edit', [MaterialController::class, 'edit']); // Misma función
Route::put('/materiales/{id}', [MaterialController::class, 'update']); // Misma función
Route::delete('/materiales/{id}', [MaterialController::class, 'destroy']); // Misma función
Route::post('/materiales/{id}/aumentar', [MaterialController::class, 'aumentarExistencia']); // Misma función
Route::post('/materiales/import', [MaterialController::class, 'importCardex']); // Misma función

/* =========================
   RUTAS DE COMPATIBILIDAD
========================= */
Route::get('/index_materiales_simple', [MaterialController::class, 'indexSimple']);

/* ====================
   CRUD: LUGARES
==================== */
Route::prefix('lugares')->name('lugares.')->group(function () {
   Route::get('/', [LugarController::class, 'index'])->name('index');
   Route::get('/create', [LugarController::class, 'create'])->name('create');
   Route::post('/', [LugarController::class, 'store'])->name('store');
   Route::get('/{id_lugar}', [LugarController::class, 'show'])->name('show');
   Route::get('/{id_lugar}/edit', [LugarController::class, 'edit'])->name('edit');
   Route::put('/{id_lugar}', [LugarController::class, 'update'])->name('update');
   Route::delete('/{id_lugar}', [LugarController::class, 'destroy'])->name('destroy');
   Route::get('/{id}/usuarios', [FallaController::class, 'usuariosPorLugar'])->middleware('auth')->name('usuarios');
});

/* =========================
   REPORTE DE FALLOS (CEDIS)
========================= */
Route::get('/reporte', function () {
   return view('reporte_fallo');
})->name('reporte.form');

Route::post('/reporte', [ReporteController::class, 'enviar'])->name('reporte.enviar');

/* =========================
   SISTEMA DE FALLAS
========================= */
Route::prefix('fallas')->name('fallas.')->group(function () {
   Route::get('/', [FallaController::class, 'index'])->name('index');
   Route::get('/{id}', [FallaController::class, 'show'])->name('show');
   Route::post('/', [FallaController::class, 'store'])->name('store');
   Route::post('/enviar/{id}', [FallaController::class, 'enviar'])->name('enviar');
   Route::get('/pdf/{id}', [FallaController::class, 'pdf'])->name('pdf');
   Route::get('/materiales', [FallaController::class, 'getAllMaterials'])->name('materiales');
   Route::get('/search-materials', [FallaController::class, 'searchMaterials'])->name('search-materials');
});

/* =========================
   RUTAS ESPECIALES
========================= */
Route::get('/reportes', [FallaController::class, 'showReportes'])->name('reportes.index');
Route::post('/verificar-password', [FallaController::class, 'verificarPassword'])->name('verificar-password');
Route::get('/materials/search', [FallaController::class, 'searchMaterials'])->name('materials.search');