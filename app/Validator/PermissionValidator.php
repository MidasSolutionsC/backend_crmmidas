<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionValidator {

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
      'nombre' => 'required|string|max:35|unique:permisos,nombre,' . $this->id,
      'descripcion' => 'nullable|string'
    ];
  }
  
}


?>