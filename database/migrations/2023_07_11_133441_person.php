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
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('nombres', 60);
            $table->string('apellido_paterno', 60);
            $table->string('apellido_materno', 60);
            $table->foreignId('paises_id');
            $table->foreignId('distritos_id')->nullable();
            $table->foreignId('tipo_documentos_id');
            $table->string('documento', 11);
            $table->string('reverso_documento', 250)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono', 11)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('direccion', 100)->nullable();
            $table->unique(['tipo_documentos_id', 'documento']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('paises_id')->references('id')->on('paises');
            $table->foreign('distritos_id')->references('id')->on('distritos');
            $table->foreign('tipo_documentos_id')->references('id')->on('tipo_documentos');
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
