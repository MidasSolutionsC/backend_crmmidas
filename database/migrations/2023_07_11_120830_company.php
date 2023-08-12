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
        Schema::create('empresas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('paises_id');
            $table->foreignId('distritos_id')->nullable();
            $table->string('razon_social', 80);
            $table->string('nombre_comercial', 80)->nullable();
            $table->mediumText('descripcion')->nullable();
            $table->foreignId('tipo_documentos_id');
            $table->string('documento', 11);
            $table->string('tipo_empresa', 30)->nullable();
            $table->string('direccion', 250)->nullable();
            $table->string('ciudad', 60)->nullable();
            $table->string('telefono', 11)->nullable();
            $table->string('correo', 100)->unique();
            $table->foreignId('user_create_id')->nullable();            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->boolean('estado')->default(true);
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
        Schema::dropIfExists('empresas');
    }
};
