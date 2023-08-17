<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeUserPermissionValidator {

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
      'permisos_id' => 'required|integer',
      'tipo_usuarios_id' => 'required|integer',
      'is_active' => 'nullable|boolean',
    ];
  }
}


?>