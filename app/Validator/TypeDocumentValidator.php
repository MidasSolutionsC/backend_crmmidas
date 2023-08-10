<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeDocumentValidator {

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
      'nombre' => 'required|unique:tipo_documentos,nombre,' . $this->request->input('id'),
      'abreviacion' => 'required'
    ];
  }
  
  private function rulesUpdate(){
    return [
      'nombre' => 'required',
      'abreviacion' => 'required'
    ];
  }

  private function messages(){
    return [
      'nombre.required' => 'El :attribute es requerido.',
      'nombre.unique' => 'El :attribute ya existe en la base de datos',
      'abreviacion.required' => 'La :attribute es requerido.',
    ];
  }
}


?>