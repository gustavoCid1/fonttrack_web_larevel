<?php

/**
 * Migración para la creación de la tabla tb_notificaciones
 * 
 * Esta migración establece el sistema de workflow y aprobación
 * para las notificaciones de fallas vehiculares. Implementa un
 * flujo completo de estados desde el reporte inicial hasta la
 * aprobación administrativa, incluyendo control de materiales,
 * trazabilidad de usuarios y optimización de consultas.
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
     * Ejecuta la migración para crear la tabla tb_notificaciones
     * 
     * Define la estructura completa del sistema de workflow incluyendo:
     * - Información detallada de vehículos y fallas
     * - Sistema de estados y aprobaciones administrativas
     * - Control completo de usuarios reportantes y aprobadores
     * - Gestión de materiales con trazabilidad
     * - Optimización de consultas mediante índices estratégicos
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::create('tb_notificaciones', function (Blueprint $table) {
            // Campo clave primaria con auto-incremento
            $table->bigIncrements('id_notificacion');
            
            // Relación con tabla de lugares/ubicaciones
            $table->unsignedBigInteger('id_lugar');

            // === INFORMACIÓN DEL VEHÍCULO ===
            // Número económico del vehículo
            $table->string('eco')->nullable();
            
            // Placas de circulación del vehículo
            $table->string('placas')->nullable();
            
            // Marca del vehículo
            $table->string('marca')->nullable();
            
            // Año de fabricación del vehículo
            $table->string('anio')->nullable();
            
            // Kilometraje actual del vehículo
            $table->string('km')->nullable();
            
            // Fecha en que ocurrió la falla
            $table->date('fecha')->nullable();
            
            // Nombre del conductor responsable del vehículo
            $table->string('nombre_conductor')->nullable();

            // === DESCRIPCIÓN DEL PROBLEMA ===
            // Descripción detallada de la falla reportada
            $table->text('descripcion')->nullable();
            
            // Observaciones adicionales sobre el incidente
            $table->text('observaciones')->nullable();

            // === USUARIO QUE REPORTA LA FALLA ===
            // ID del usuario que genera el reporte inicial
            $table->unsignedBigInteger('usuario_reporta_id');
            
            // Nombre del usuario reportante para referencia
            $table->string('nombre_usuario_reporta');
            
            // Correo del usuario reportante para notificaciones
            $table->string('correo_usuario_reporta');

            // === CONTROL DE MATERIALES ===
            // Resumen textual de materiales solicitados
            $table->string('material')->nullable();
            
            // Cantidad total a descontar del inventario
            $table->integer('cantidad')->default(0);
            
            // Almacenamiento del JSON completo de materiales
            $table->text('materials')->nullable();

            // === CONFIGURACIÓN ADMINISTRATIVA ===
            // Correo destino para envío de reportes
            $table->string('correo_destino')->nullable();
            
            // === SISTEMA DE ESTADOS Y WORKFLOW ===
            // Estado actual de la notificación en el flujo de aprobación
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            
            // === INFORMACIÓN DE APROBACIÓN ADMINISTRATIVA ===
            // ID del administrador que procesa la notificación
            $table->unsignedBigInteger('usuario_aprueba_id')->nullable();
            
            // Nombre del administrador que aprueba
            $table->string('nombre_usuario_aprueba')->nullable();
            
            // Correo del administrador para confirmaciones
            $table->string('correo_usuario_aprueba')->nullable();
            
            // Firma digital del usuario autorizador
            $table->string('autorizado_por')->nullable();
            
            // Identificación del revisor técnico
            $table->string('reviso_por')->nullable();
            
            // Timestamp de cuando se completó la aprobación
            $table->timestamp('fecha_aprobacion')->nullable();
            
            // Comentarios del administrador sobre la decisión
            $table->text('comentarios_admin')->nullable();

            // Campos de auditoría automática
            $table->timestamps();

            // === CONFIGURACIÓN DE CLAVES FORÁNEAS ===
            // Relación con lugares - eliminación en cascada
            $table->foreign('id_lugar')
                  ->references('id_lugar')
                  ->on('tb_lugares')
                  ->onDelete('cascade');

            // Relación con usuario reportante - eliminación en cascada
            $table->foreign('usuario_reporta_id')
                  ->references('id_usuario')
                  ->on('tb_users')
                  ->onDelete('cascade');

            // Relación con usuario aprobador - preserva datos históricos
            $table->foreign('usuario_aprueba_id')
                  ->references('id_usuario')
                  ->on('tb_users')
                  ->onDelete('set null');

            // === ÍNDICES PARA OPTIMIZACIÓN DE PERFORMANCE ===
            // Índice para consultas por estado
            $table->index('estado');
            
            // Índice compuesto para reportes por estado y fecha
            $table->index(['estado', 'created_at']);
            
            // Índice compuesto para consultas por lugar y estado
            $table->index(['id_lugar', 'estado']);
            
            // Índice para consultas por usuario reportante
            $table->index('usuario_reporta_id');
            
            // Índice para búsquedas por fecha de falla
            $table->index('fecha');
        });
    }

    /**
     * Revierte la migración eliminando la tabla tb_notificaciones
     * 
     * Método de rollback que permite deshacer los cambios
     * realizados por esta migración, eliminando completamente
     * la tabla de notificaciones y todas sus dependencias.
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_notificaciones');
    }
};