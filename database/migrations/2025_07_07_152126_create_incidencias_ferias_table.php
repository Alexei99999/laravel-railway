<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidenciasFeriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidencias_ferias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feria_id');
            $table->string('cedula', 15)->nullable();
            $table->string('trabajador', 100)->nullable();
            $table->string('ubicacion', 150)->nullable();
            $table->text('incidencia', 300)->nullable();
            $table->text('fecha_incidencia')->nullable();
            $table->text('hora_incidencia')->nullable();
            $table->timestamps();

            $table->foreign('feria_id')->references('id')->on('ferias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidencias_ferias');
    }
}
