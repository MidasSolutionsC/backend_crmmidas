<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleDetailValidator {
  
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
      'promociones_id' => 'nullable|integer',
      'productos_id' => 'required|integer|unique:ventas_detalles,productos_id,' . $this->id . ',id,deleted_at,NULL,ventas_id,' . $this->request->input('ventas_id'),
      'cantidad' => 'nullable|integer',
      'instalaciones_id' => 'nullable|integer',
      'tipo_estados_id' => 'nullable|integer',
      'datos_json' => 'nullable|array',
      'observacion' => 'nullable|string',
      'fecha_cierre' => 'nullable|date:Y-m-d',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
      'is_active' => 'nullable|boolean',
    ];
  }
}

?>