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
        Schema::create('tmp_ventas_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ventas_id');    
            $table->foreignId('ventas_detalles_id')->nullable();        
            $table->string('nombre', 70);
            $table->string('tipo', 10);
            $table->string('archivo', 100);
            $table->foreignId('user_create_id');            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('ventas_id')->references('id')->on('tmp_ventas');
            $table->foreign('ventas_detalles_id')->references('id')->on('tmp_ventas_detalles');
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
        Schema::dropIfExists('tmp_ventas_documentos');
    }
};
