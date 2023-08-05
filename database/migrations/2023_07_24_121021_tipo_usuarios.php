<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    
    public function up(){
        Schema::create('tipo_usuarios', function (Blueprint $table) {
            $table->engine= 'InnoDB';

            $table->id();
            $table->string('nombre')->unique();
            $table->mediumText('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(){
        Schema::dropIfExists('tipo_usuarios');
    }
};