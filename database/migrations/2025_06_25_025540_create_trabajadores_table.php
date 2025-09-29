<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrabajadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->string('cedula', 8)->unique();
            $table->string('telefono', 11)->nullable();
            $table->string('apellido1', 20);
            $table->string('apellido2', 20)->nullable();
            $table->string('nombre1', 15);
            $table->string('nombre2', 15)->nullable();
            $table->string('e_mail', 50);
            $table->string('rol', 21);
            $table->string('e_registro', 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trabajadores');
    }
}
