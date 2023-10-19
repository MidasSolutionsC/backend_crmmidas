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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresas_id')->nullable();
            $table->foreignId('personas_id')->nullable();
            $table->text('domicilio')->nullable();
            $table->string('tipo', 30);
            $table->string('direccion', 250);
            $table->string('numero', 6)->nullable();
            $table->string('escalera', 100)->nullable();
            $table->string('portal', 100)->nullable();
            $table->string('planta', 100)->nullable();
            $table->string('puerta', 100)->nullable();
            $table->string('codigo_postal', 5);
            $table->string('localidad', 90)->nullable();
            $table->string('provincia', 90)->nullable();
            $table->string('territorial', 90)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('empresas_id')->references('id')->on('empresas');
            $table->foreign('personas_id')->references('id')->on('personas');
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
        Schema::dropIfExists('direcciones');
    }
};
