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
        Schema::create('calendarios', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 50);
            $table->text('descripcion')->nullable();
            $table->string('color', 25);
            $table->date('fecha_inicio');
            $table->time('hora_inicio');
            $table->date('fecha_final');
            $table->time('hora_final');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_seen')->default(false);
            $table->foreignId('user_create_id');            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('calendarios');
    }
};
