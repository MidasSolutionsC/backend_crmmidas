<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
      'tipo_servicios_id' => 'nullable|integer',
      'tipo_producto' => 'required|string|size:1|in:F,S',
      'marcas_id' => 'nullable|integer',
      // 'nombre' => 'required|max:80|unique:promociones,nombre,' . $this->id . ',id,tipo_servicios_id,' . $this->request->input('tipo_servicios_id') . ',deleted_at,NULL',
      'nombre' => [
        'required',
        'string',
        'max:80',
        Rule::unique('promociones', 'nombre')
        ->ignore($this->id, 'id')
        ->where(function ($query) {
          if ($this->request->has('tipo_servicios_id')) {
              $query->where('tipo_servicios_id', $this->request->input('tipo_servicios_id'));
          } 
          
          if ($this->request->has('marcas_id')) {
              $query->where('marcas_id', $this->request->input('marcas_id'));
          }
        }),
      ],
      'descripcion' => 'nullable|string',
      'tipo_monedas_id' => 'nullable|integer',
      'tipo_descuento' => 'string|in:C,P',
      'descuento' => 'nullable|numeric|max:9999',
      'fecha_inicio' => 'required|date:Y-m-d',
      'fecha_fin' => 'nullable|date:Y-m-d',
      'codigo' => 'nullable|string|max:30',
      'cantidad_minima' => 'nullable|integer',
      'cantidad_maxima' => 'nullable|integer',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
      'is_private' => 'nullable|boolean',
      'is_active' => 'nullable|boolean',
    ];
  }
}


?>