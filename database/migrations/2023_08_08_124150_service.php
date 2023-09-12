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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_servicios_id');
            $table->foreignId('productos_id');
            $table->foreignId('promociones_id')->nullable();
            $table->string('nombre', 80);
            $table->text('descripcion')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unique(['tipo_servicios_id', 'productos_id', 'nombre']);
            $table->foreignId('user_create_id')->nullable();            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->foreign('tipo_servicios_id')->references('id')->on('tipo_servicios');
            $table->foreign('productos_id')->references('id')->on('productos');
            $table->foreign('promociones_id')->references('id')->on('promociones');
            $table->foreign('user_create_id')->references('id')->on('usuarios');
            $table->foreign('user_update_id')->references('id')->on('usuarios');
            $table->foreign('user_delete_id')->references('id')->on('usuarios');
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('servicios');
    }
};
