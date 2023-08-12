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
        Schema::create('distritos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('provincias_id');
            $table->char('ubigeo_codigo', 6);
            $table->string('nombre', 100);
            $table->char('provincias_codigo', 4);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['provincias_id', 'nombre']);
            $table->foreign('provincias_id')->references('id')->on('provincias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distritos');
    }
};
