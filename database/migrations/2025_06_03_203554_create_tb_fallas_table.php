<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbFallasTable extends Migration
{
    public function up()
    {
        Schema::create('tb_fallas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lugar'); // Relación con lugares

            // Campos nuevos añadidos:
            $table->string('eco')->nullable();
            $table->string('placas')->nullable();
            $table->string('marca')->nullable();
            $table->string('anio')->nullable();  // Se guarda el "ano" en la columna "anio"
            $table->string('km')->nullable();
            $table->date('fecha')->nullable();
            $table->string('nombre_conductor')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('reviso_por')->nullable();

            // Campos ya existentes
            $table->string('material'); // Resumen de materiales
            $table->integer('cantidad'); // Cantidad descontada o ajustada
            $table->string('autorizado_por'); // Firma del admin (usuario que autoriza)
            $table->string('correo_destino')->nullable(); // Para envío del reporte

            // Almacenar el JSON original de materiales (opcional)
            $table->text('materials')->nullable();

            $table->timestamps();

            // Llave foránea
            $table->foreign('id_lugar')
                  ->references('id_lugar')
                  ->on('tb_lugares')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_fallas');
    }
}
