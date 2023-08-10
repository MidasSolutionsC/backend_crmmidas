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
        Schema::create('clientes', function (Blueprint $table) {
            $table->engine = 'InnoDB';  
            $table->id();
            $table->foreignId('tipo_documentos_id');
            $table->string('documento', 11);
            $table->string('nombres', 60);
            $table->string('paterno', 60);
            $table->string('materno', 60);
            $table->mediumText('reverso_documento')->nullable();
            $table->date('fecha_nacimiento');
            $table->string('nacionalidad', 60)->nullable();
            $table->mediumText('domicilio_east1')->nullable();
            $table->string('tipo', 30)->nullable();
            $table->string('direccion', 250)->nullable();
            $table->string('numero', 6)->nullable();
            $table->string('escalera', 100)->nullable();
            $table->string('portal', 100)->nullable();
            $table->string('planta', 100)->nullable();
            $table->string('puerta', 100)->nullable();
            $table->char('tipo_cliente', 2);
            $table->char('persona_juridica', 2);
            $table->string('razon_social', 90)->nullable();
            $table->char('cif', 9)->nullable();
            $table->char('codigo_postal', 5);
            $table->string('localidad', 150);
            $table->string('provincia', 150);
            $table->string('codigo_carga', 100);
            $table->string('territorial', 90);
            $table->string('telefono_principal', 11);
            $table->string('movil', 11)->nullable();
            $table->string('fijo', 11)->nullable();
            $table->string('correo', 100)->unique();
            $table->string('cta_bco', 100);
            $table->string('segmento_vodafond', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('user_create_id')->nullable();            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->foreign('user_create_id')->references('id')->on('usuarios');
            $table->foreign('user_update_id')->references('id')->on('usuarios');
            $table->foreign('user_delete_id')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};
