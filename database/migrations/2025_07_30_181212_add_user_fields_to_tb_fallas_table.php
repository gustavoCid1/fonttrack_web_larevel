<?php

/**
 * Migración para agregar campos de usuario reportante a la tabla tb_fallas
 * 
 * Esta migración extiende la tabla tb_fallas agregando información
 * del usuario que reporta la falla vehicular, estableciendo trazabilidad
 * completa del proceso desde el reporte inicial hasta la resolución.
 * Incluye relación con la tabla de usuarios y datos de contacto.
 * 
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 * @since Laravel 10.48.29
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para agregar campos de usuario reportante
     * 
     * Modifica la tabla tb_fallas agregando:
     * - Relación con el usuario que reporta la falla
     * - Información de contacto del usuario reportante
     * - Trazabilidad del proceso de reporte
     * - Integridad referencial con manejo de eliminación segura
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::table('tb_fallas', function (Blueprint $table) {
            // Relación con el usuario que reporta la falla (opcional)
            $table->unsignedBigInteger('usuario_reporta_id')->nullable()->after('id_lugar');
            
            // Nombre del usuario que reporta para referencia rápida
            $table->string('nombre_usuario_reporta')->nullable()->after('usuario_reporta_id');
            
            // Correo electrónico del usuario reportante para notificaciones
            $table->string('correo_usuario_reporta')->nullable()->after('nombre_usuario_reporta');
            
            // Configuración de clave foránea hacia tb_users
            // Utiliza 'set null' para preservar registros históricos
            $table->foreign('usuario_reporta_id')
                  ->references('id_usuario')
                  ->on('tb_users')
                  ->onDelete('set null');
        });
    }

    /**
     * Revierte la migración eliminando los campos de usuario reportante
     * 
     * Método de rollback que deshace los cambios realizados,
     * eliminando la relación con usuarios y los campos agregados.
     * Mantiene la integridad de la base de datos durante el proceso.
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::table('tb_fallas', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la columna
            $table->dropForeign(['usuario_reporta_id']);
            
            // Eliminar los campos agregados en orden inverso
            $table->dropColumn([
                'usuario_reporta_id', 
                'nombre_usuario_reporta', 
                'correo_usuario_reporta'
            ]);
        });
    }
};