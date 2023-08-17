<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistrictValidator {
  
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
      'provincias_id' => 'required|integer',
      'ubigeo_codigo' => 'required|string|size:6|unique:distritos,ubigeo_codigo,'. $this->id,
      'provincias_codigo' => 'required|string|size:4',
      'nombre' => 'required|string|max:100|unique:distritos,nombre,'. $this->id, ',id,provincias_id' . $this->request->input('provincias_id'),
    ];
  }
  
}

?>