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
        Schema::create('tipo_usuarios_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permisos_id');
            $table->foreignId('tipo_usuarios_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['permisos_id', 'tipo_usuarios_id']);
            $table->foreign('permisos_id')->references('id')->on('permisos');
            $table->foreign('tipo_usuarios_id')->references('id')->on('tipo_usuarios');
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
        Schema::dropIfExists('tipo_usuarios_permisos');
    }
};
