<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class Tipousuario extends Migration{
    
    public function up(){
        Schema::create('tipousuario', function (Blueprint $table) {
            $table->engine= 'InnoDB';

            $table->id();
            $table->string('nombre')->unique();
            $table->mediumText('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->dateTime('fecharegistro')->default(Carbon::now());
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(){
        Schema::dropIfExists('tipousuario');
    }
}