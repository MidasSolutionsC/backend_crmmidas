<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampusValidator {
  
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
      'paises_id' => 'required|integer',
      'codigo_ubigeo' => 'nullable|string',
      'nombre' => 'required|string|max:100|unique:sedes,nombre,'. $this->id . ',id,deleted_at,NULL',
      'ciudad' => 'nullable|string|max:50',
      'direccion' => 'nullable|string|max:250',
      'codigo_postal' => 'nullable|string|max:6',
      'telefono' => 'nullable|string|max:11',
      'correo' => 'nullable|string|max:100',
      'responsable' => 'nullable|string|max:100',
      'fecha_apertura' => 'required|date:Y-m-d',
      'is_active' => 'nullable|boolean',
      'logo' => 'nullable|string|max:100',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
      'file' => 'nullable|file|max:10240', // 10 megabytes.
    ];
  }

}

?>