<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryValidator {
  
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
      'iso_code' => 'required|string|size:2|unique:paises,iso_code,'. $this->request->input('id'),
      'nombre' => 'required|unique:paises,nombre,'. $this->request->input('id'),
    ];
  }

  private function rulesUpdate(){
    return [
      'iso_code' => 'required',
      'nombre' => 'required'
    ];
  }
  
  private function messages(){
    return [
      'iso_code' => [
        'required' => 'El :attribute es requerido.',
        'unique' => 'El :attribute ya existe en la base de datos.',
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