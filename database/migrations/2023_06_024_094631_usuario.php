<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Usuario extends Migration{
    
    public function up(){
        Schema::create('usuario', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('nombres');
            $table->string('paterno');
            $table->string('materno');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(){
        Schema::dropIfExists('usuario');
    }
}