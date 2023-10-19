<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TmpSaleDocumentValidator{
  
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
      'tipo_documentos_id' => 'nullable|integer',
      'nombre' => 'required|string|max:70',
      'archivo' => 'nullable|string|max:100',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'integer',
      'user_delete_id' => 'integer',
      'is_active' => 'boolean',
    ];
  }

}


?>