<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressValidator {
  
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
      'tipo' => 'required|string|max:30',
      'direccion' => 'required|string|max:250',
      'localidad' => 'string|max:90',
      'provincia' => 'string|max:90',
      'codigo_postal' => 'required|max:5',
      'territorial' => 'string|max:90',
    ];
  }

  private function rulesUpdate(){
    return [
      'tipo' => 'required|string|max:30',
      'direccion' => 'required|string|max:250',
      'localidad' => 'string|max:90',
      'provincia' => 'string|max:90',
      'codigo_postal' => 'required|max:5',
      'territorial' => 'string|max:90',
    ];
  }
  
  private function messages(){
    return [
      'tipo' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'max' => 'El :attribute debe ser como máximo de :max caracteres.',
      ],
      'direccion' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'max' => 'El :attribute debe ser como máximo de :max caracteres.',
      ],
      'localidad' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'max' => 'El :attribute debe ser como máximo de :max caracteres.',
      ],
      'provincia' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'max' => 'El :attribute debe ser como máximo de :max caracteres.',
      ],
      'codigo_postal' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'max' => 'El :attribute debe ser como máximo de :max caracteres.',
      ],
      'territorial' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'max' => 'El :attribute debe ser como máximo de :max caracteres.',
      ],
    ];

  }
}

?>