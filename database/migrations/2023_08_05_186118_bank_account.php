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
        Schema::create('cuentas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clientes_id');
            $table->foreignId('tipo_cuentas_bancarias_id');
            $table->string('cuenta', 30);
            $table->date('fecha_apertura')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_create_id');            
            $table->foreignId('user_update_id')->nullable();            
            $table->foreignId('user_delete_id')->nullable(); 
            $table->unique(['clientes_id', 'tipo_cuentas_bancarias_id', 'cuenta'], 'clientes_id_tipo_cuentas_cuenta_unique');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('clientes_id')->references('id')->on('clientes');
            $table->foreign('tipo_cuentas_bancarias_id')->references('id')->on('tipo_cuentas_bancarias');
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
        Schema::dropIfExists('cuentas_bancarias');
    }
};
