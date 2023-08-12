<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProvinceValidator {
  
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
      'departamentos_id' => 'required|integer',
      'ubigeo_codigo' => 'required|string|size:4',
      'departamentos_codigo' => 'required|string|size:2',
      'nombre' => 'required|unique:provincias,nombre,'. $this->request->input('id'),
    ];
  }

  private function rulesUpdate(){
    return [
      'departamentos_id' => 'required|integer',
      'ubigeo_codigo' => 'required|string|size:4',
      'departamentos_codigo' => 'required|string|size:2',
      'nombre' => 'required'
    ];
  }
  
  private function messages(){
    return [
      'departamentos_id' => [
        'required' => 'El :attribute es requerido.',
        'integer' => 'El :attribute no es un numero valido.',
      ],
      'ubigeo_codigo' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un string valido.',
        'size' => 'El :attribute debe ser de 4 caracteres.',
      ],
      'departamentos_codigo' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un string valido.',
        'size' => 'El :attribute debe ser de 2 caracteres.',
      ],
      'nombre' => [
        'required' => 'El :attribute es requerido.',
        'unique' => 'El :attribute ya existe en la base de datos.',
      ],
    ];

  }
}

?>