<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberValidator {

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
      'grupos_id' => 'required',
      'usuarios_id' => 'required|unique:integrantes,grupos_id,NULL,id,usuarios_id,' . $this->request->input('usuarios_id')
    ];
  }
  
  private function rulesUpdate(){
    return [
      'grupos_id' => 'required',
      'usuarios_id' => 'required|unique:integrantes,grupos_id,NULL,id,usuarios_id,' . $this->request->input('usuarios_id'),
      // 'usuarios_id' => 'required',

    ];
  }

  private function messages(){
    return [
      'grupos_id.required' => 'El :attribute es requerido.',
      'usuarios_id.required' => 'El :attribute es requerido.',
      'usuarios_id.unique' => 'El :attribute ya existe en la base de datos.',
    ];
  }
}


?>