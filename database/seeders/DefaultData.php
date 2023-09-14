<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Países
        DB::table('paises')->insert([
            'nombre' => 'Perú',
            'iso_code' => 'PE',
        ]);

        // Tipo de documentos
        DB::table('tipo_estados')->insert([
            'nombre' => 'Pendiente',
        ]);

        // Tipo de documentos
        DB::table('tipo_documentos')->insert([
            'nombre' => 'Documento nacional de identidad',
            'abreviacion' => 'DNI',
        ]);

        // Tipo de usuario
        DB::table('tipo_usuarios')->insert([
            'nombre' => 'Invitado',
        ]);

        // Tipo de servicios
        DB::table('tipo_servicios')->insert([
            'nombre' => 'Linea Movil',
        ]);
        DB::table('tipo_servicios')->insert([
            'nombre' => 'Linea Fija',
        ]);

        // Monedas
        DB::table('divisas')->insert([
            'nombre' => 'Soles',
            'iso_code' => 'PEN',
            'simbolo' => 'S/.',
        ]);

        // Categorias
        DB::table('categorias')->insert([
            'nombre' => 'Telecomunicaciones',
        ]);

        // Marcas
        DB::table('marcas')->insert([
            'nombre' => 'Empresa Entel',
        ]);

        DB::table('marcas')->insert([
            'nombre' => 'Empresa Movistar',
        ]);

        DB::table('marcas')->insert([
            'nombre' => 'Empresa Vodafone',
        ]);
    }
}
