<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductValidator{
  
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
      'tipo_servicios_id' => 'required|integer',
      'nombre' => 'required|string|max:80|unique:productos,nombre,' . $this->id . ',id,tipo_servicios_id,' . $this->request->input('tipo_servicios_id'),
      'descripcion' => 'nullable|string' ,
      'precio' => 'nullable|numeric' ,
      'user_create_id' => 'nullable|integer' ,
      'user_update_id' => 'nullable|integer' ,
      'user_delete_id' => 'nullable|integer' ,
      'is_active' => 'nullable|boolean' ,
    ];
  }
}


?>