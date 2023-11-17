<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceValidator{

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

  private function rules() {
    return [
      'nombre' => 'required|string|max:60|unique:servicios,nombre,' . $this->id . ',id,deleted_at,NULL',
      'descripcion' => 'nullable|string' ,
      'tipo_servicios_id' => 'required|integer',
      'productos_id' => 'required|integer',
      'promociones_id' => 'nullable|integer',
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'integer',
      'user_update_id' => 'integer',
      'user_delete_id' => 'integer',
    ];
  }
}
