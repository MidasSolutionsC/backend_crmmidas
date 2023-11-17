<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TmpSaleHistoryValidator{
  
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
      'ventas_detalles_id' => 'nullable|integer',
      'tipo' => 'required|string|size:1',
      'tipo_estados_id' => 'required|integer',
      'fecha' => 'nullable|date:Y-m-d',
      'hora' => ['nullable', 'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
      'comentario' => 'string',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'integer',
      'user_delete_id' => 'integer',
      'is_active' => 'boolean',
    ];
  }
}


?>