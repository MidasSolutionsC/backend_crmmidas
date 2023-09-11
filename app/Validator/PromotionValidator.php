<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromotionValidator{
  
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
      'tipo_servicios_id' => 'required|integer',
      'nombre' => 'required|max:80|unique:promociones,nombre,' . $this->id . ',id,tipo_servicios_id,' . $this->request->input('tipo_servicios_id') . ',deleted_at,NULL',
      'descripcion' => 'string',
      'tipo_descuento' => 'string|in:C,P',
      'descuento' => 'numeric|max:9999',
      'fecha_inicio' => 'required|date:Y-m-d',
      'fecha_fin' => 'nullable|date:Y-m-d',
      'codigo' => 'string|max:30',
      'cantidad_minima' => 'integer',
      'cantidad_maxima' => 'integer',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
      'is_private' => 'nullable|boolean',
      'is_active' => 'nullable|boolean',
    ];
  }
}


?>