<?php

/**
 * Migración para la creación de la tabla tb_vehiculos
 * 
 * Esta migración establece el catálogo maestro de vehículos del sistema,
 * centralizando toda la información vehicular que anteriormente se
 * almacenaba de forma dispersa. Proporciona una base de datos normalizada
 * para el control de flotilla, estados de vehículos y asignación de
 * conductores, optimizando la gestión integral del parque vehicular.
 * 
 * @author Daniela Pérez Peralta
 * @version 2.0.0
 * @since Laravel 10.48.29
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculosTable extends Migration
{
    /**
     * Ejecuta la migración para crear la tabla tb_vehiculos
     * 
     * Define la estructura del catálogo maestro de vehículos incluyendo:
     * - Identificación única por número económico
     * - Información técnica y administrativa completa
     * - Sistema de estados para control operativo
     * - Asignación de conductores habituales
     * - Relación con ubicaciones geográficas
     * - Índices optimizados para consultas frecuentes
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('tb_vehiculos', function (Blueprint $table) {
            // Campo clave primaria con auto-incremento
            $table->id();
            
            // Relación con tabla de lugares/ubicaciones
            $table->unsignedBigInteger('id_lugar');

            //  IDENTIFICACIÓN PRINCIPAL DEL VEHÍCULO 
            // Número económico único del vehículo (identificador principal)
            $table->string('eco')->unique();
            
            // Placas de circulación oficiales
            $table->string('placas')->nullable();
            
            // Marca del fabricante del vehículo
            $table->string('marca')->nullable();
            
            // Año de fabricación o modelo
            $table->string('anio')->nullable();
            
            // Kilometraje actual registrado en el odómetro
            $table->integer('kilometraje')->default(0);
            
            // Nombre del conductor asignado habitualmente
            $table->string('conductor_habitual')->nullable();

            //  INFORMACIÓN TÉCNICA ADICIONAL 
            // Modelo específico del vehículo
            $table->string('modelo')->nullable();
            
            // Color principal del vehículo
            $table->string('color')->nullable();
            
            // Estado operativo actual del vehículo
            $table->enum('estatus', ['activo', 'inactivo', 'mantenimiento'])->default('activo');

            // Campos de auditoría automática
            $table->timestamps();

            // CONFIGURACIÓN DE CLAVE FORÁNEA 
            // Relación con lugares - eliminación en cascada
            $table->foreign('id_lugar')
                ->references('id_lugar')
                ->on('tb_lugares')
                ->onDelete('cascade');

            //  ÍNDICES PARA OPTIMIZACIÓN DE PERFORMANCE 
            // Índice compuesto para consultas por lugar y estado
            $table->index(['id_lugar', 'estatus']);
            
            // Índice para búsquedas rápidas por número económico
            $table->index('eco');
        });
    }

    /**
     * Revierte la migración eliminando la tabla tb_vehiculos
     * 
     * Método de rollback que permite deshacer los cambios
     * realizados por esta migración, eliminando completamente
     * el catálogo de vehículos. NOTA: Verificar dependencias
     * antes de ejecutar rollback en producción.
     * 
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_vehiculos');
    }
}