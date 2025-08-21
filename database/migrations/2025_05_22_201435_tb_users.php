<?php

/**
 * Migración para la creación de la tabla tb_users
 * 
 * Esta migración establece la estructura base para el almacenamiento 
 * de usuarios del sistema, incluyendo sus datos personales, 
 * credenciales de acceso y relaciones con otras entidades.
 * 
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 * @since Laravel 10.48.29
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Ejecuta la migración para crear la tabla tb_users
     * 
     * Define la estructura completa de la tabla de usuarios incluyendo:
     * - Campos de identificación y datos personales
     * - Sistema de autenticación
     * - Relaciones con otras tablas del sistema
     * - Configuración de integridad referencial
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::create('tb_users', function (Blueprint $table) {
            // Campo clave primaria con auto-incremento
            $table->bigIncrements('id_usuario');

            // Datos personales del usuario
            $table->string('nombre', 255);

            // Campo para autenticación - único en el sistema
            $table->string('correo', 255)->unique();

            // Contraseña encriptada para acceso al sistema
            $table->string('password', 255);

            // Clasificación del usuario (admin, cliente, etc.)
            $table->integer('tipo_usuario');

            // Imagen de perfil del usuario (opcional)
            $table->string('foto_usuario')->nullable();

            // Relación con tabla de lugares (opcional)
            $table->unsignedBigInteger('id_lugar')->nullable();

            // Campos de auditoría automática
            $table->timestamps();

            // Configuración de clave foránea hacia tb_lugares
            // Establece integridad referencial con eliminación y actualización en cascada
            $table->foreign('id_lugar')
                ->references('id_lugar')
                ->on('tb_lugares')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte la migración eliminando la tabla tb_users
     * 
     * Método de rollback que permite deshacer los cambios
     * realizados por esta migración, eliminando completamente
     * la tabla y sus dependencias.
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_users');
    }
};