<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientValidator {
  
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
      'personas_id' => 'integer',
      'empresas_id' => 'integer',
      'tipo_cliente' => 'required|string|size:2',
      'cif' => 'nullable|string|size:9',
      'codigo_carga' => 'nullable|string|max:100',
      'segmento_vodafond' => 'nullable|string|max:30',
      'cta_bco' => 'nullable|string|max:100',
      'user_create_id' => 'required|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
      'is_active' => 'nullable|boolean',
    ];
  }
}

?>