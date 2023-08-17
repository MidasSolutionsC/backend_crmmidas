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
        Schema::create('instalaciones', function (Blueprint $table) {
            $table->engine = 'InnoDB';  
            $table->id();
            $table->foreignId('ventas_id'); 
            $table->string('tipo', 20);
            $table->string('direccion', 200);
            $table->string('numero', 20);
            $table->string('escalera', 70);
            $table->string('portal', 70);
            $table->string('planta', 70);
            $table->string('puerta', 20);
            $table->string('codigo_postal', 20);
            $table->string('localidad', 70);
            $table->string('provincia', 70);
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_create_id');            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('ventas_id')->references('id')->on('ventas');
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
        Schema::dropIfExists('instalaciones');
    }
};
