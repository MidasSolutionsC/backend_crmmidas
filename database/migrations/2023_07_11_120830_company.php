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
            $table->id();
            $table->foreignId('paises_id');
            $table->char('codigo_ubigeo', 6)->nullable();
            $table->string('razon_social', 80);
            $table->string('nombre_comercial', 80)->nullable();
            $table->text('descripcion')->nullable();
            // $table->foreignId('tipo_documentos_id');
            // $table->string('documento', 11);
            $table->string('tipo_empresa', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_create_id')->nullable();            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            // $table->unique(['tipo_documentos_id', 'documento']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('paises_id')->references('id')->on('paises');
            $table->foreign('codigo_ubigeo')->references('ubigeo')->on('ubigeos');
            // $table->foreign('tipo_documentos_id')->references('id')->on('tipo_documentos');
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
        Schema::dropIfExists('empresas');
    }
};
