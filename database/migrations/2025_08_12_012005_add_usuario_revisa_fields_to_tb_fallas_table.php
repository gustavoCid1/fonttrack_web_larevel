<?php

/**
 * Migración para agregar campos de usuario revisor a la tabla tb_fallas
 * 
 * Esta migración extiende la tabla tb_fallas agregando información
 * del usuario técnico que realiza la revisión y validación de la falla.
 * Establece una segunda capa de trazabilidad en el proceso, separando
 * las funciones de reporte y revisión técnica para mayor control
 * de calidad en el sistema de gestión de fallas vehiculares.
 * 
 * @author Daniela Pérez Peralta
 * @version 2.0.0
 * @since Laravel 10.48.29
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para agregar campos de usuario revisor
     * 
     * Modifica la tabla tb_fallas agregando:
     * - Identificación del usuario técnico revisor
     * - Información de contacto del revisor para seguimiento
     * - Separación de responsabilidades entre reporte y revisión
     * - Índice de optimización para consultas por revisor
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::table('tb_fallas', function (Blueprint $table) {
            // ID del usuario técnico que realiza la revisión de la falla
            $table->unsignedBigInteger('usuario_revisa_id')->nullable()->after('correo_usuario_reporta');
            
            // Nombre del usuario revisor para referencia rápida
            $table->string('nombre_usuario_revisa')->nullable()->after('usuario_revisa_id');
            
            // Correo electrónico del revisor para notificaciones técnicas
            $table->string('correo_usuario_revisa')->nullable()->after('nombre_usuario_revisa');
            
            // Índice para optimizar consultas por usuario revisor
            $table->index('usuario_revisa_id');
        });
    }

    /**
     * Revierte la migración eliminando los campos de usuario revisor
     * 
     * Método de rollback que deshace los cambios realizados,
     * eliminando la información del usuario revisor y el índice
     * asociado. Mantiene la integridad de datos durante el proceso.
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::table('tb_fallas', function (Blueprint $table) {
            // Eliminar el índice antes de eliminar la columna
            $table->dropIndex(['usuario_revisa_id']);
            
            // Eliminar los campos de usuario revisor
            $table->dropColumn([
                'usuario_revisa_id',
                'nombre_usuario_revisa',
                'correo_usuario_revisa'
            ]);
        });
    }
};