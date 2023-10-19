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
        Schema::create('ventas_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ventas_id'); 
            $table->foreignId('ventas_detalles_id')->nullable(); 
            $table->char('tipo', 1)->default('A');
            $table->foreignId('tipo_estados_id');  
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->time('hora')->default(DB::raw('CURRENT_TIME'));
            $table->text('comentario')->nullable();
            $table->foreignId('user_create_id');            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('ventas_id')->references('id')->on('ventas');
            $table->foreign('ventas_detalles_id')->references('id')->on('ventas_detalles');
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
        Schema::dropIfExists('ventas_historial');
    }
};
