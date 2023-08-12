<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyValidator {
  
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
      'razon_social' => 'required|string|max:80',
      'tipo_documentos_id' => 'required|integer',
      'documento' => 'required|string|max:11|unique:empresas,documento,tipo_documentos_id,' . $this->request->input('id'),
      'correo' => 'required|email|unique:empresas,correo,'. $this->request->input('id'),
    ];
  }

  private function rulesUpdate(){
    return [
      'paises_id' => 'required',
      'razon_social' => 'required|string|max:80',
      'tipo_documentos_id' => 'required',
      'documento' => 'required|string',
      'correo' => 'required|email'
    ];
  }

  
  private function messages(){
    return [
      'paises_id' => [
        'required' => 'El :attribute es requerido.',  
        'integer' => 'El :attribute no es un numero valido.',
      ],
      'razon_social' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'size' => 'El :attribute debe ser de 6 caracteres.',
      ],
      'tipo_documentos_id' => [
        'required' => 'El :attribute es requerido.',
        'unique' => 'El :attribute ya existe en la base de datos.',
        'integer' => 'El :attribute no es un numero valido.',
      ],
      'documento' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'unique' => 'El :attribute ya existe en la base de datos.',
        'max' => 'El :attribute debe ser de 11 caracteres como máximo.',
      ],
      'correo' => [
        'required' => 'El :attribute es requerido.',
        'unique' => 'El :attribute ya existe en la base de datos.',
        'email' => 'El :attribute no es un email valido.',
      ],
    ];

  }
}

?>