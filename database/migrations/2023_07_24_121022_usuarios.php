<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    
    public function up(){
        Schema::create('usuarios', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->foreignId('grupos_id')->nullable()->default(0);
            $table->foreignId('tipo_usuarios_id');
            $table->string('nombres', 60);
            $table->string('paterno', 60);
            $table->string('materno', 60);
            $table->foreignId('tipo_documentos_id');
            $table->string('documento', 11);
            $table->string('correo', 100);
            $table->string('clave', 100);
            $table->date('fecha_nacimiento');
            $table->string('celular', 11)->nullable()->default('');
            $table->string('direccion', 100)->nullable()->default('');
            $table->string('foto', 100)->nullable()->default('');
            $table->string('api_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('logueado')->default(false);
            $table->boolean('estado')->default(true);
            $table->dateTime('ultima_conexion')->nullable();
            $table->unique(['tipo_documentos_id', 'documento']);
            //$table->foreign('grupos_id')->references('id')->on('grupos');
            $table->foreign('tipo_usuarios_id')->references('id')->on('tipo_usuarios');
            $table->foreign('tipo_documentos_id')->references('id')->on('tipo_documentos');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(){
        Schema::dropIfExists('usuarios');
    }
};