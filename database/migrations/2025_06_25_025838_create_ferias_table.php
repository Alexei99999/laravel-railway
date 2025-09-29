<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeriasTable extends Migration
{
    public function up()
    {
        Schema::create('ferias', function (Blueprint $table) {
            $table->id();
            $table->string('cod_edo', 50)->nullable();
            $table->string('estado', 255)->nullable();
            $table->string('cod_mun', 50)->nullable();
            $table->string('municipio', 255)->nullable();
            $table->string('cod_parroquia', 50)->nullable();
            $table->string('parroquia', 255)->nullable();
            $table->string('cod_centro', 50)->nullable();
            $table->string('nombre_pto', 255)->nullable();
            $table->string('direccion_pto', 255)->nullable();
            $table->string('rectoria', 50)->nullable();
            $table->string('cedula', 20)->nullable();
            $table->string('apellidos', 255)->nullable();
            $table->string('nombres', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 255)->nullable();
            $table->string('rol', 50)->nullable();
            $table->string('status_contact1', 50)->nullable();
            $table->string('fecha_hora1', 50)->nullable();
            $table->string('status_contact2', 50)->nullable();
            $table->string('fecha_hora2', 50)->nullable();
            $table->string('status_contact3', 50)->nullable();
            $table->string('fecha_hora3', 50)->nullable();
            $table->string('disponibilidad', 50)->nullable();
            $table->string('incidencias', 50)->nullable();
            $table->string('fecha_incidencia', 50)->nullable();
            $table->string('hora_incidencia', 50)->nullable();
            $table->string('observaciones', 500)->nullable();
            $table->enum('e_registro', ['Activo', 'Inactivo']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ferias');
    }
}
