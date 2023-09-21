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
            $table->char('codigo_ubigeo', 6)->nullable();
            $table->foreignId('tipo_documentos_id');
            $table->string('documento', 11);
            $table->string('reverso_documento', 250)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            // $table->string('telefono', 11)->nullable();
            // $table->string('correo', 100)->nullable();
            // $table->string('direccion', 100)->nullable();
            $table->unique(['tipo_documentos_id', 'documento']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('paises_id')->references('id')->on('paises');
            $table->foreign('codigo_ubigeo')->references('ubigeo')->on('ubigeos');
            $table->foreign('tipo_documentos_id')->references('id')->on('tipo_documentos');
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
