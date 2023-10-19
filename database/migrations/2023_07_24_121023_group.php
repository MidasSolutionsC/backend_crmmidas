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
            $table->id();
            $table->string('nombre', 70);
            $table->text('descripcion')->nullable();
            $table->foreignId('sedes_id');            
            $table->foreignId('user_create_id');            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable();  
            $table->boolean('is_active')->default(true);          
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('sedes_id')->references('id')->on('sedes');
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
        Schema::dropIfExists('grupos');
    }
};
