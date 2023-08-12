<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientValidator {
  
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
      'personas_id' => 'integer',
      'empresas_id' => 'integer',
      'tipo_cliente' => 'required|string|size:2'
    ];
  }

  private function rulesUpdate(){
    return [
      'personas_id' => 'integer',
      'empresas_id' => 'integer',
      'tipo_cliente' => 'required|string|size:2'
    ];
  }
  
  private function messages(){
    return [
      'personas_id' => [
        'required' => 'El :attribute es requerido.',
        'integer' => 'El :attribute no es un numero valido.',
      ],
      'empresas_id' => [
        'required' => 'El :attribute es requerido.',
        'integer' => 'El :attribute no es un numero valido.',
      ],
      'tipo_cliente' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'size' => 'El :attribute debe ser de :size caracteres.',
      ],
    ];

  }
}

?>