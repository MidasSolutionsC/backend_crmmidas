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
        Schema::create('documentos_identificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personas_id')->nullable();
            $table->foreignId('empresas_id')->nullable();
            $table->foreignId('tipo_documentos_id');
            $table->string('documento', 20);
            $table->string('reverso_documento', 250)->nullable();
            $table->foreignId('user_create_id')->nullable();            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->boolean('is_primary')->default(false); 
            $table->boolean('is_active')->default(true); 
            $table->unique(['personas_id', 'tipo_documentos_id', 'documento'], 'personas_documento_unique');
            $table->unique(['empresas_id', 'tipo_documentos_id', 'documento'], 'empresas_documento_unique');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('personas_id')->references('id')->on('personas');
            $table->foreign('empresas_id')->references('id')->on('empresas');
            $table->foreign('tipo_documentos_id')->references('id')->on('tipo_documentos');
            $table->foreign('user_create_id')->references('id')->on('usuarios');
            $table->foreign('user_update_id')->references('id')->on('usuarios');
            $table->foreign('user_delete_id')->references('id')->on('usuarios');
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
        Schema::dropIfExists('documentos_identificaciones');
    }
};
