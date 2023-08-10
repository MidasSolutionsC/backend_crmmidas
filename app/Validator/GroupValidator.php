<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupValidator{
  

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
      'nombre' => 'required|unique:tipo_usuarios,nombre,id,' . $this->request->input('id'),
    ];
  }

  private function rulesUpdate(){
    return [
      'nombre' => 'required',
    ];
  }

  private function messages(){
    return [
      'nombre.required' => 'El :attribute es requerido.',
      'nombre.unique' => 'El :attribute ya existe en la base de datos.',
      'descripcion.required' => 'La :attribute es requerido.',
    ];
  }
}


?>