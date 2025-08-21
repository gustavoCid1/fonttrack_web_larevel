<?php

/**
 * Migración para la creación de la tabla tb_fallas
 * 
 * Esta migración establece la estructura para el registro y control
 * de fallas vehiculares del sistema. Incluye información detallada
 * de vehículos, descripción de fallas, control de materiales utilizados,
 * proceso de autorización y generación de reportes.
 * 
 * @author Gustavo Angel Cid Flores
 * @version 2.0.0
 * @since Laravel 10.48.29
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbFallasTable extends Migration
{
    /**
     * Ejecuta la migración para crear la tabla tb_fallas
     * 
     * Define la estructura completa del sistema de registro de fallas incluyendo:
     * - Información detallada de vehículos afectados
     * - Datos del incidente y responsables
     * - Control de materiales y recursos utilizados
     * - Sistema de autorización y supervisión
     * - Funcionalidad de reportes y notificaciones
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('tb_fallas', function (Blueprint $table) {
            // Campo clave primaria con auto-incremento
            $table->id();
            
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

            // === INFORMACIÓN DEL INCIDENTE ===
            // Fecha en que ocurrió la falla
            $table->date('fecha')->nullable();
            
            // Nombre del conductor responsable del vehículo
            $table->string('nombre_conductor')->nullable();
            
            // Descripción detallada de la falla ocurrida
            $table->text('descripcion')->nullable();
            
            // Observaciones adicionales sobre el incidente
            $table->text('observaciones')->nullable();
            
            // Persona que revisó y validó la falla
            $table->string('reviso_por')->nullable();

            // === CONTROL DE MATERIALES ===
            // Resumen textual de materiales utilizados
            $table->string('material');
            
            // Cantidad total descontada o ajustada del inventario
            $table->integer('cantidad');
            
            // Usuario administrativo que autoriza el uso de materiales
            $table->string('autorizado_por');
            
            // Correo electrónico para envío de reportes
            $table->string('correo_destino')->nullable();

            // Almacenamiento del JSON completo de materiales utilizados
            $table->text('materials')->nullable();

            // Campos de auditoría automática
            $table->timestamps();

            // Configuración de clave foránea hacia tb_lugares
            // Establece integridad referencial con eliminación en cascada
            $table->foreign('id_lugar')
                  ->references('id_lugar')
                  ->on('tb_lugares')
                  ->onDelete('cascade');
        });
    }

    /**
     * Revierte la migración eliminando la tabla tb_fallas
     * 
     * Método de rollback que permite deshacer los cambios
     * realizados por esta migración, eliminando completamente
     * la tabla de registro de fallas y sus dependencias.
     * 
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_fallas');
    }
}