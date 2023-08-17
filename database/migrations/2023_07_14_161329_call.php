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
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('numero', 11);
            $table->string('operador', 30);
            $table->string('operador_llamo', 30)->nullable();
            $table->string('tipificacion', 30)->nullable();
            $table->string('nombres', 60)->nullable();
            $table->string('apellido_paterno', 60)->nullable();
            $table->string('apellido_materno', 60)->nullable();
            $table->string('direccion', 250)->nullable();
            $table->string('permanencia', 150)->nullable();
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
            $table->foreign('tipo_estados_id')->references('id')->on('tipo_estados');
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
        Schema::dropIfExists('llamadas');
    }
};
