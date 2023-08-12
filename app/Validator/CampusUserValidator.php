<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampusUserValidator {
  
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
      'sedes_id' => 'required',
      'usuarios_id' => 'required',
    ];
  }

  private function rulesUpdate(){
    return [
      'sedes_id' => 'required',
      'usuarios_id' => 'required',
    ];
  }
  
  private function messages(){
    return [
      'sedes_id' => [
        'required' => 'El :attribute es requerido.'
      ],
      'usuarios_id' => [
        'required' => 'El :attribute es requerido.',
      ],
    ];

  }
}

?>