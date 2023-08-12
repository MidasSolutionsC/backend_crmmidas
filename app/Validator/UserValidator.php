<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserValidator {
  
  private $request;

  public function __construct(Request $request = null) {
    $this->request = $request;
  }

  public function validate(string $process = 'create'){
    if($process == 'auth'){
      return Validator::make($this->request->all(), $this->rulesAuth(), $this->messages());
    }
    if($process == 'create'){
      return Validator::make($this->request->all(), $this->rulesCreate(), $this->messages());
    }
    if($process == 'update'){
      return Validator::make($this->request->all(), $this->rulesUpdate(), $this->messages());
    }
  }

  private function rulesCreate(){
    return [
      'personas_id' => 'required|integer',
      'tipo_usuarios_id' => 'required|integer',
      'nombre_usuario' => 'required|string|max:20|unique:usuarios,nombre_usuario,'. $this->request->input('id'),
      'clave' => 'required',
    ];
  }

  private function rulesUpdate(){
    return [
      'personas_id' => 'required|integer',
      'tipo_usuarios_id' => 'required|integer',
      'nombre_usuario' => 'required|string|max:20',
      'clave' => 'required|max:100',
    ];
  }

  private function rulesAuth(){
    return [
      'nombre_usuario' => 'required|string|max:20',
      'clave' => 'required|max:100'
    ];
  }
  
  private function messages(){
    return [
      'personas_id' => [
        'required' => 'El :attribute es requerido.',
        'integer' => 'El :attribute no es un numero valido.',
      ],
      'tipo_usuarios_id' => [
        'required' => 'El :attribute es requerido.',
        'integer' => 'El :attribute no es un numero valido.',
      ],
      'nombre_usuario' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'size' => 'El :attribute debe tener :size caracteres.',
        'max' => 'El :attribute debe tener como máximo :max caracteres.',
      ],
      'clave' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'size' => 'El :attribute debe tener :size caracteres.',
        'max' => 'El :attribute debe tener como máximo :max caracteres.',
      ],
    ];

  }
}

?>