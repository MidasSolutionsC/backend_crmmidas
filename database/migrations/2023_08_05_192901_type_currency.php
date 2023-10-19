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
        Schema::create('tipo_monedas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paises_id')->nullable();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->char('iso_code', 3);
            $table->string('simbolo', 10);
            $table->double('tasa_cambio', 8, 2)->nullable();
            $table->string('formato_moneda', 30)->nullable();
            $table->date('fecha_actualizado')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_create_id')->nullable();
            $table->foreignId('user_update_id')->nullable();
            $table->foreignId('user_delete_id')->nullable();
            $table->foreign('paises_id')->references('id')->on('paises');
            $table->foreign('user_create_id')->references('id')->on('usuarios');
            $table->foreign('user_update_id')->references('id')->on('usuarios');
            $table->foreign('user_delete_id')->references('id')->on('usuarios');
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('tipo_monedas');
    }
};
