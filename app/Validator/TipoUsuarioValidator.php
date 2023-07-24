<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoUsuarioValidator{
  

  /**
   * @var Request
   */
  private $request;

  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function validate(){
    return Validator::make($this->request->all(), $this->rules(), $this->messages());
  }

  private function rules(){
    return [
      'nombre' => 'required|unique:tipousuario,nombre,' . $this->request->id,
    ];
  }

  private function messages(){
    return [
      'nombre.required' => 'El nombre es requerido.',
      'nombre.unique' => 'El nombre no puede duplicarse en la base de datos.',
      'descripcion.required' => 'La descripción es requerido.',
    ];
  }
}


?>