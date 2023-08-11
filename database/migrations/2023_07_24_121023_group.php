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
        Schema::create('grupos', function (Blueprint $table) {
            $table->engine = 'InnoDB';  
            $table->id();
            $table->string('nombre', 70);
            $table->mediumText('descripcion')->nullable();
            $table->foreignId('coordinador_id')->nullable();            
            $table->foreignId('supervisor_id')->nullable();            
            $table->foreignId('user_create_id')->nullable();            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable();  
            $table->boolean('estado')->default(true);          
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('coordinador_id')->references('id')->on('usuarios');
            $table->foreign('supervisor_id')->references('id')->on('usuarios');
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
        Schema::dropIfExists('grupos');
    }
};
