<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TmpSaleDetailValidator {
  
  private $request;
  private $id;

  public function __construct(Request $request = null) {
    if ($request) {
      $this->request = $request;
      $this->id = $request->route('id');
    }
  }

  public function setRequest(array  $data, int $id = null){
    $this->request = new Request();
    $this->request->replace($data);
    $this->id = $id;
  }

  public function validate(){
    return Validator::make($this->request->all(), $this->rules());
  }

  private function rules(){
    return [
      'ventas_id' => 'required|integer',
      'servicios_id' => 'required|integer|unique:ventas_detalles,servicios_id,' . $this->id . ',id,ventas_id,' . $this->request->input('ventas_id'),
      'instalaciones_id' => 'nullable|integer',
      'tipo_estados_id' => 'nullable|integer',
      'observacion' => 'nullable|string',
      'fecha_cierre' => 'nullable|date:Y-m-d',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'integer',
      'user_delete_id' => 'integer',
      'is_active' => 'boolean',
    ];
  }
}

?>