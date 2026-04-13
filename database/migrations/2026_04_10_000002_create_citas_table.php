<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitasTable extends Migration
{
    public function up()
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dueno_id');
            $table->unsignedBigInteger('mascota_id');
            $table->unsignedBigInteger('sucursal_id');
            $table->date('fecha_cita');
            $table->time('hora_cita');
            $table->text('prediagnostico')->nullable();
            $table->timestamps();

            $table->foreign('dueno_id')->references('id')->on('duenos')->onDelete('cascade');
            $table->foreign('mascota_id')->references('id')->on('mascotas')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
            $table->unique(['sucursal_id', 'fecha_cita', 'hora_cita'], 'citas_sucursal_fecha_hora_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('citas');
    }
}