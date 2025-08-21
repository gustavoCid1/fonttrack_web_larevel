<?php

/**
 * Migración para la creación de la tabla tb_lugares
 * 
 * Esta migración establece la estructura base para el almacenamiento 
 * de ubicaciones geográficas del sistema. Funciona como tabla de 
 * referencia para otras entidades que requieren asociación con 
 * ubicaciones específicas como usuarios y materiales.
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
     * Ejecuta la migración para crear la tabla tb_lugares
     * 
     * Define la estructura de la tabla de lugares incluyendo:
     * - Identificación única de ubicaciones
     * - Información geográfica básica
     * - Estructura de referencia para otras tablas del sistema
     * - Campos de auditoría temporal
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::create('tb_lugares', function (Blueprint $table) {
            // Campo clave primaria con auto-incremento
            $table->bigIncrements('id_lugar');
            
            // Nombre descriptivo del lugar o ubicación
            $table->string('nombre');
            
            // Estado o entidad federativa donde se ubica (opcional)
            $table->string('estado')->nullable();
            
            // Campos de auditoría automática
            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla tb_lugares
     * 
     * Método de rollback que permite deshacer los cambios
     * realizados por esta migración. NOTA: Esta tabla es 
     * referenciada por otras tablas, por lo que debe eliminarse
     * después de las tablas dependientes.
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_lugares');
    }
};