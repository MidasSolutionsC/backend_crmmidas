<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CallValidator {
  
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
      'numero' => 'required|string|max:11',
      'operadores_id' => 'required|integer',
      'operadores_llamo_id' => 'integer',
      'tipificaciones_llamadas_id' => 'integer',
      'nombres' => 'string|max:60',
      'apellido_paterno' => 'string|max:60',
      'apellido_materno' => 'string|max:60',
      'direccion' => 'string|max:250',
      'permanencia' => 'boolean',
      'permanencia_tiempo' => 'string|max:150',
      'fecha' => 'nullable|date:Y-m-d',
      'hora' => ['nullable', 'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
      'tipo_estados_id' => 'nullable|integer',
      'is_active' => 'nullable|boolean',
    ];
  }
}

?>