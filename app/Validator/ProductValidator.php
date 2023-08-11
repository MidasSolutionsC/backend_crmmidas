<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductValidator{
  

  /**
   * @var Request
   */
  private $request;

  public function __construct(Request $request)
  {
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
      'tipo_servicios_id' => 'required',
      'nombre' => 'required|unique:productos,nombre,tipo_servicios_id,id,' . $this->request->input('id')
    ];
  }

  private function rulesUpdate(){
    return [
      'tipo_servicios_id' => 'required',
      'nombre' => 'required'
    ];
  }

  private function messages(){
    return [
      'tipo_servicios_id.required' => 'El :attribute es requerido.',
      'nombre.required' => 'La :attribute es requerido.',
      'nombre.unique' => 'El :attribute ya existe en la base de datos.',
    ];
  }
}


?>