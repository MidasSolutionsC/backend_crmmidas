<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeCurrencyValidator{

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
      'paises_id' => 'nullable|integer',
      'nombre' => 'nullable|string|unique:tipo_monedas,nombre,' . $this->id . ',id,deleted_at,NULL',
      'descripcion' => 'nullable|string' ,
      'tasa_cambio' => 'nullable|numeric|max:99999999.99|regex:/^\d+(\.\d{1,2})?$/',
      'iso_code' => 'required|string|max:3' ,
      'simbolo' => 'required|string|max:10' ,
      'formato_moneda' => 'nullable|string|max:30' ,
      'fecha_actualizado' => 'nullable|date:Y-m-d',
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
    ];
  }
}


?>