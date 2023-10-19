<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('llamadas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 11);
            $table->foreignId('operadores_id');
            $table->foreignId('operadores_llamo_id')->nullable();
            $table->foreignId('tipificaciones_llamadas_id')->nullable();
            $table->string('nombres', 60)->nullable();
            $table->string('apellido_paterno', 60)->nullable();
            $table->string('apellido_materno', 60)->nullable();
            $table->string('direccion', 250)->nullable();
            $table->boolean('permanencia')->default(false);
            $table->string('permanencia_tiempo', 150)->nullable();
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->time('hora')->default(DB::raw('CURRENT_TIME'));
            $table->foreignId('user_create_id');            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->foreignId('tipo_estados_id')->nullable(); 
            $table->boolean('is_active')->default(true); 
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('operadores_id')->references('id')->on('operadores');
            $table->foreign('operadores_llamo_id')->references('id')->on('operadores');
            $table->foreign('tipificaciones_llamadas_id')->references('id')->on('tipificaciones_llamadas');
            $table->foreign('tipo_estados_id')->references('id')->on('tipo_estados');
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
        Schema::dropIfExists('llamadas');
    }
};
