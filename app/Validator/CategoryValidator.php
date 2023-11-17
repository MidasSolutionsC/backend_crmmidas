<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryValidator{
  
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
      'nombre' => 'required|string|max:60|unique:categorias,nombre,' . $this->id . ',id,deleted_at,NULL',
      'descripcion' => 'nullable|string' ,
      'user_create_id' => 'nullable|integer' ,
      'user_update_id' => 'nullable|integer' ,
      'user_delete_id' => 'nullable|integer' ,
      'is_active' => 'nullable|boolean' ,
    ];
  }
}


?>