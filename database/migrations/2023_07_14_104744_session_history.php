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
        Schema::create('sesiones_historiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuarios_id');
            $table->string('dispositivo', 50);
            $table->text('descripcion');
            $table->string('ip_address', 50);
            $table->string('so', 30);
            $table->string('navegador', 50);
            $table->boolean('login')->default(false);
            $table->char('tipo', 1)->default('S');
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->time('hora')->default(DB::raw('CURRENT_TIME'));
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('usuarios_id')->references('id')->on('usuarios');
            $table->engine  = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sesiones_historiales');
    }
};
