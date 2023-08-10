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
            $table->engine = 'InnoDB';  
            $table->id();
            $table->foreignId('ventas_id');  
            $table->char('tipo', 1)->default('A');
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->time('hora')->default(DB::raw('CURRENT_TIME'));
            $table->mediumText('comentario')->nullable();
            $table->boolean('estado')->default(true);
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
        Schema::dropIfExists('ventas_historial');
    }
};
