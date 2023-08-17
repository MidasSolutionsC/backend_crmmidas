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
        Schema::create('provincias', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('departamentos_id');
            $table->char('ubigeo_codigo', 4)->unique();
            $table->string('nombre', 100);
            $table->char('departamentos_codigo', 2);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['departamentos_id', 'nombre']);
            $table->foreign('departamentos_id')->references('id')->on('departamentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provincias');
    }
};
