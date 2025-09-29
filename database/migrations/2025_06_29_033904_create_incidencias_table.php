<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidenciasTable extends Migration
{
    public function up()
    {
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->string('trabajador', 100);
            $table->string('ubicacion', 150);
            $table->enum('e_contact', ['Contactado', 'No contactado']);
            $table->date('fecha_rep');
            $table->enum('e_disponib', ['Trabaja', 'No trabaja', 'Sin información']);
            $table->date('fecha_e_disponib')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('incidencias');
    }
}
