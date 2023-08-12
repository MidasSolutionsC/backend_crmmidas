<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampusValidator {
  
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
      'paises_id' => 'required|integer',
      'codigo_postal' => 'required|string:6',
      'fecha_apertura' => 'required|date:Y-m-d',
      'nombre' => 'required|unique:sedes,nombre,'. $this->request->input('id'),
    ];
  }

  private function rulesUpdate(){
    return [
      'paises_id' => 'required|integer',
      'codigo_postal' => 'required|string:6',
      'fecha_apertura' => 'required|date:Y-m-d',
      'nombre' => 'required'
    ];
  }
  
  private function messages(){
    return [
      'paises_id' => [
        'required' => 'El :attribute es requerido.',
        'integer' => 'El :attribute no es un numero valido.',
      ],
      'codigo_postal' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un string valido.',
        'size' => 'El :attribute debe ser de :size caracteres.',
      ],
      'fecha_apertura' => [
        'required' => 'El :attribute es requerido.',
        'date' => 'El formato de fecha de :attribute debe ser Y-m-d.',
      ],
      'nombre' => [
        'required' => 'El :attribute es requerido.',
        'unique' => 'El :attribute ya existe en la base de datos.',
      ],
    ];

  }
}

?>