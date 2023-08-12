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
        Schema::create('clientes', function (Blueprint $table) {
            $table->engine = 'InnoDB';  
            $table->id();
            $table->foreignId('personas_id')->nullable();
            $table->foreignId('empresas_id')->nullable();
            $table->char('tipo_cliente', 2);
            $table->char('cif', 9)->nullable();
            $table->string('codigo_carga', 100)->nullable();
            $table->string('segmento_vodafond', 30)->nullable();
            $table->string('cta_bco', 100)->nullable();
            $table->foreignId('user_create_id')->nullable();            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('personas_id')->references('id')->on('personas');
            $table->foreign('empresas_id')->references('id')->on('empresas');
            $table->foreign('user_create_id')->references('id')->on('usuarios');
            $table->foreign('user_update_id')->references('id')->on('usuarios');
            $table->foreign('user_delete_id')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};
