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
            $table->id();
            $table->foreignId('empresas_id')->nullable();
            $table->foreignId('personas_id')->nullable();
            $table->char('tipo', 3);
            $table->string('contacto', 80);
            $table->unique(['empresas_id', 'contacto']);
            $table->unique(['personas_id', 'contacto']);
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
        Schema::dropIfExists('contactos');
    }
};
