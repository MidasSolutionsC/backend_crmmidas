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
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paises_id');
            $table->char('codigo_ubigeo', 6)->nullable();
            $table->string('nombre', 100);
            $table->string('ciudad', 50)->nullable();
            $table->string('direccion', 250)->nullable();
            $table->string('codigo_postal', 6)->nullable();
            $table->string('telefono', 11)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('responsable', 100)->nullable();
            $table->date('fecha_apertura');
            $table->boolean('is_active')->default(true);
            $table->string('logo', 100)->nullable();
            $table->foreignId('user_create_id')->nullable();            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['paises_id', 'nombre']);
            $table->foreign('paises_id')->references('id')->on('paises');
            $table->foreign('codigo_ubigeo')->references('ubigeo')->on('ubigeos');
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
        Schema::dropIfExists('sedes');
    }
};
