<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->char('tipo_producto', 1); // F (Físico) S(Servicio)
            $table->foreignId('tipo_servicios_id')->nullable();
            $table->foreignId('marcas_id')->nullable();
            $table->string('nombre', 80);
            $table->text('descripcion')->nullable();
            $table->enum('tipo_descuento', ['C', 'P'])->default('C');
            $table->foreignId('tipo_monedas_id')->nullable();
            $table->double('descuento', 8, 2)->default(0);
            $table->date('fecha_inicio')->default(DB::raw('CURRENT_DATE'));
            $table->date('fecha_fin')->nullable();
            $table->string('codigo', 30)->nullable();
            $table->tinyInteger('cantidad_minima')->nullable();
            $table->integer('cantidad_maxima')->nullable();
            $table->foreignId('user_create_id');            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->boolean('is_private')->default(false);
            $table->boolean('is_active')->default(true);
            // $table->unique(['tipo_servicios_id', 'nombre']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tipo_servicios_id')->references('id')->on('tipo_servicios');
            $table->foreign('marcas_id')->references('id')->on('marcas');
            $table->foreign('tipo_monedas_id')->references('id')->on('tipo_monedas');
            $table->foreign('user_create_id')->references('id')->on('usuarios');
            $table->foreign('user_update_id')->references('id')->on('usuarios');
            $table->foreign('user_delete_id')->references('id')->on('usuarios');
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
        Schema::dropIfExists('promociones');
    }
};
