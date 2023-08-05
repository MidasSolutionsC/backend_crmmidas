<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoDocumentoValidator {

  private $request;

  public function __construct(Request $request) {
    $this->request = $request;
  }

  public function validate(string $process = 'create'){
    if($process == 'create'){
      return Validator::make($this->request->all(), $this->rules(), $this->messages());
    }
    if($process == 'update'){
      return Validator::make($this->request->all(), $this->rulesUpdate(), $this->messages());
    }
  }
  
  private function rules(){
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
      'nombre.required' => 'El nombre es requerido.',
      'nombre.unique' => 'El nombre ya existe en la base de datos',
      'abreviacion.required' => 'La abreviación es requerido.',
    ];
  }
}


?>