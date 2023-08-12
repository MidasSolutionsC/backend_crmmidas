<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonValidator {
  
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
      'nombres' => 'required|string|max:60',
      'apellido_paterno' => 'required|string|max:60',
      'apellido_materno' => 'required|string|max:60',
      'paises_id' => 'required|integer',
      'tipo_documentos_id' => 'required|integer',
      'documento' => 'required|string|max:11|unique:personas,documento,tipo_documentos_id,' . $this->request->input('id'),
    ];
  }

  private function rulesUpdate(){
    return [
      'nombres' => 'required|string|max:60',
      'apellido_paterno' => 'required|string|max:60',
      'apellido_materno' => 'required|string|max:60',
      'paises_id' => 'required|integer',
      'tipo_documentos_id' => 'required|integer',
      'documento' => 'required|string',
    ];
  }
  
  private function messages(){
    return [
      'nombres' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'max' => 'El :attribute debe tener como máximo :max caracteres.',
      ],
      'apellido_paterno' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'max' => 'El :attribute debe tener como máximo :max caracteres.',
      ],
      'apellido_materno' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'max' => 'El :attribute debe tener como máximo :max caracteres.',
      ],
      'paises_id' => [
        'required' => 'El :attribute es requerido.',
        'integer' => 'El :attribute no es un numero entero valido.',
      ],
      'tipo_documentos_id' => [
        'required' => 'El :attribute es requerido.',
        'integer' => 'El :attribute no es un numero entero valido.',
      ],
      'documento' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'unique' => 'El :attribute ya existe en la base de datos.',
        'max' => 'El :attribute debe tener como máximo :max caracteres.',
      ],
      'fecha_nacimiento' => [
        'required' => 'El :attribute es requerido.',
        'email' => 'El :attribute no es un email valido.',
        'unique' => 'El :attribute ya existe en la base de datos.',
      ],
      'fecha_nacimiento' => [
        'required' => 'El :attribute es requerido.',
        'date' => 'El formato de fecha de :attribute debe ser Y-m-d.',
      ],
    ];

  }
}

?>