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
        Schema::create('integrantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupos_id');
            $table->foreignId('usuarios_id');
            $table->boolean('is_active')->default(true);
            $table->unique(['grupos_id', 'usuarios_id']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('grupos_id')->references('id')->on('grupos');
            $table->foreign('usuarios_id')->references('id')->on('usuarios');
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
        Schema::dropIfExists('integrantes');
    }
};
