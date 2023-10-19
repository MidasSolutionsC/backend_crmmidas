<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductValidator{
  
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
      'categorias_id' => 'nullable|integer',
      'marcas_id' => 'nullable|integer',
      'tipo_producto' => 'required|string|size:1|in:F,S',
      // 'nombre' => 'required|string|max:80|unique:productos,nombre,' . $this->id . ',id,deleted_at,NULL,tipo_servicios_id,' . $this->request->input('tipo_servicios_id'),
      'nombre' => [
        'required',
        'string',
        'max:80',
        Rule::unique('productos', 'nombre')
        ->ignore($this->id, 'id')
        ->where(function ($query) {
          if ($this->request->has('tipo_servicios_id')) {
              $query->where('tipo_servicios_id', $this->request->input('tipo_servicios_id'));
          } 
          
          if ($this->request->has('marcas_id')) {
              $query->where('marcas_id', $this->request->input('marcas_id'));
          }
        }),
        // ->where(function ($query) {
        //     $query->where('tipo_servicios_id', $this->request->input('tipo_servicios_id'));
        //     $query->where('marcas_id', $this->request->input('marcas_id'));
        // }),
      ],
      'especificaciones' => 'nullable|string' ,
      'descripcion' => 'nullable|string' ,
      'user_create_id' => 'nullable|integer' ,
      'user_update_id' => 'nullable|integer' ,
      'user_delete_id' => 'nullable|integer' ,
      'is_active' => 'nullable|boolean' ,
    ];
  }
}


?>