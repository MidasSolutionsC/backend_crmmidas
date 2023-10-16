<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 60);
            $table->string('apellido_paterno', 60);
            $table->string('apellido_materno', 60);
            $table->foreignId('paises_id');
            $table->string('nacionalidad', 80)->nullable();
            $table->char('codigo_ubigeo', 6)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('paises_id')->references('id')->on('paises');
            $table->foreign('codigo_ubigeo')->references('ubigeo')->on('ubigeos');
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
};
