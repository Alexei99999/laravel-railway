<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidenciasFiscalizacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidencias_fiscalizacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscalizacion_id');
            $table->string('cedula', 15)->nullable();
            $table->string('trabajador', 100)->nullable();
            $table->string('ubicacion', 150)->nullable();
            $table->string('contacto', 150)->nullable();
            $table->text('incidencia', 300)->nullable();
            $table->date('fecha_incidencia')->nullable();
            $table->time('hora_incidencia')->nullable();
            $table->timestamps();

            $table->foreign('fiscalizacion_id')->references('id')->on('fiscalizacions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidencias_fiscalizacions');
    }
}
