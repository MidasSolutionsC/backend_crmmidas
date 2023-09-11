<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductPriceValidator{

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
      'productos_id' => 'nullable|integer',
      'divisas_id' => 'required|integer',
      'precio' => 'required|numeric|max:9999999999.99|regex:/^\d+(\.\d{1,2})?$/',
      'fecha_inicio' => 'nullable|date:Y-m-d',
      'fecha_fin' => 'nullable|date:Y-m-d',
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
    ];
  }
}


?>