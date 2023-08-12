<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeUserPermissionValidator {

  private $request;

  public function __construct(Request $request) {
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
      'permisos_id' => 'required|integer',
      'tipo_usuarios_id' => 'required|integer',
    ];
  }
  
  private function rulesUpdate(){
    return [
      'permisos_id' => 'required|integer',
      'tipo_usuarios_id' => 'required|integer',
    ];
  }

  private function messages(){
    return [
      'permisos_id.required' => 'El :attribute es requerido.',
      'permisos_id.integer' => 'El :attribute debe ser un número entero.',
      'tipo_usuarios_id.required' => 'El :attribute es requerido.',
      'tipo_usuarios_id.integer' => 'El :attribute debe ser un número entero.',
    ];
  }
}


?>