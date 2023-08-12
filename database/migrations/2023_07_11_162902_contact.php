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
        Schema::create('contactos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('empresas_id')->nullable();
            $table->foreignId('personas_id')->nullable();
            $table->char('tipo', 3);
            $table->string('contacto', 60);
            $table->unique(['empresas_id', 'contacto']);
            $table->unique(['personas_id', 'contacto']);
            $table->boolean('es_principal')->default(false);
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('empresas_id')->references('id')->on('empresas');
            $table->foreign('personas_id')->references('id')->on('personas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contactos');
    }
};
