<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUbicacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubicacions', function (Blueprint $table) {
            $table->id();
            $table->string('estado', 20);
            $table->string('cod_est', 2)->nullable();
            $table->string('municipio', 100);
            $table->string('cod_mun', 2)->nullable();
            $table->string('parroquia', 100);
            $table->string('cod_parroq', 2)->nullable();
            $table->string('circuns', 2);
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
        Schema::dropIfExists('ubicacions');
    }
}
