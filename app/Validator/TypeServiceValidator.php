<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeServiceValidator{
  
  private $request;
  private $id;

  public function __construct(Request $request = null) {
    $this->request = $request;
    if ($request) {
      $this->id = $request->route('id');
    }
  }

  public function validate(){
    return Validator::make($this->request->all(), $this->rules());
  }

  private function rules(){
    return [
      'nombre' => 'required|unique:tipo_servicios,nombre,' . $this->id . ',id,deleted_at,NULL',
      'descripcion' => 'nullable|string',
      'icono' => 'nullable|string|max:250',
      'is_active' => 'nullable|boolean',
    ];
  }

}


?>