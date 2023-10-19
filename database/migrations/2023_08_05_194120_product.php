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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_servicios_id');
            $table->foreignId('categorias_id')->nullable();
            $table->foreignId('marcas_id')->nullable();
            $table->string('nombre', 80);
            $table->text('descripcion')->nullable();
            $table->foreignId('user_create_id')->nullable();            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->unique(['tipo_servicios_id', 'nombre']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tipo_servicios_id')->references('id')->on('tipo_servicios');
            $table->foreign('categorias_id')->references('id')->on('categorias');
            $table->foreign('marcas_id')->references('id')->on('marcas');
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
        Schema::dropIfExists('productos');
    }
};
