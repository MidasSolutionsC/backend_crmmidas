<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstallationValidator {
  
  private $request;

  public function __construct(Request $request = null) {
    $this->request = $request;
  }

  public function validate(string $process = 'create'){
    if($process == 'create'){
      return Validator::make($this->request->all(), $this->rulesCreate(), $this->messages());
    }
    if($process == 'update'){
      return Validator::make($this->request->all(), $this->rulesUpdate(), $this->messages());
    }
  }

  private function rulesCreate(){
    return [
      'ventas_id' => 'required',
      'tipo' => 'required',
      'direccion' => 'required',
      'numero' => 'required',
      'escalera' => 'required',
      'portal' => 'required',
      'planta' => 'required',
      'puerta' => 'required',
      'codigo_postal' => 'required',
      'localidad' => 'required',
      'provincia' => 'required',
    ];
  }

  private function rulesUpdate(){
    return [
      'ventas_id' => 'required',
      'tipo' => 'required',
      'direccion' => 'required',
      'numero' => 'required',
      'escalera' => 'required',
      'portal' => 'required',
      'planta' => 'required',
      'puerta' => 'required',
      'codigo_postal' => 'required',
      'localidad' => 'required',
      'provincia' => 'required',
    ];
  }
  
  private function messages(){
    return [
      'ventas_id.required' => 'El atributo :attribute es requerido.',
      'tipo.required' => 'El atributo :attribute es requerido.',
      'direccion.required' => 'El atributo :attribute es requerido.',
      'numero.required' => 'El atributo :attribute es requerido.',
      'escalera.required' => 'El atributo :attribute es requerido.',
      'portal.required' => 'El atributo :attribute es requerido.',
      'planta.required' => 'El atributo :attribute es requerido.',
      'puerta.required' => 'El atributo :attribute es requerido.',
      'codigo_postal.required' => 'El atributo :attribute es requerido.',
      'localidad.required' => 'El atributo :attribute es requerido.',
      'provincia.required' => 'El atributo :attribute es requerido.',
    ];

  }
}

?>