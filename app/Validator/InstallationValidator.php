<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstallationValidator {
  
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
      'ventas_id' => 'required|integer',
      'tipo' => 'required|string|max:20',
      'direccion' => 'required|string|max:200',
      'numero' => 'nullable|string|max:20',
      'escalera' => 'nullable|string|max:70',
      'portal' => 'nullable|string|max:70',
      'planta' => 'nullable|string|max:70',
      'puerta' => 'nullable|string|max:20',
      'codigo_postal' => 'required|string|max:20',
      'localidad' => 'nullable|string|max:70',
      'provincia' => 'required|string|max:70',
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'required|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
    ];
  }
}

?>