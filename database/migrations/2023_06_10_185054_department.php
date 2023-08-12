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
        Schema::create('departamentos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('paises_id');
            $table->char('ubigeo_codigo', 2);
            $table->string('nombre', 80);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['paises_id', 'ubigeo_codigo']);
            $table->unique(['paises_id', 'nombre']);
            $table->foreign('paises_id')->references('id')->on('paises');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departamentos');
    }
};
