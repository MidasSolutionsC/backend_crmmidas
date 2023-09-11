<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    
    public function up(){
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personas_id');
            $table->foreignId('tipo_usuarios_id');
            $table->string('nombre_usuario', 20)->unique();
            $table->string('clave', 100);
            $table->string('foto_perfil', 100)->nullable();
            $table->text('api_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('session_activa')->default(false);
            $table->boolean('is_active')->default(true);
            $table->dateTime('ultima_conexion')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('personas_id')->references('id')->on('personas');
            $table->foreign('tipo_usuarios_id')->references('id')->on('tipo_usuarios');
            $table->engine = 'InnoDB';  
        });
    }

    public function down(){
        Schema::dropIfExists('usuarios');
    }
};