<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProvinceValidator {
  
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
      'departamentos_id' => 'required|integer',
      'ubigeo_codigo' => 'required|string|size:4',
      'departamentos_codigo' => 'required|string|size:2',
      'nombre' => 'required|string|max:100|unique:provincias,nombre,'. $this->id, ',id,departamentos_id,' . $this->request->input('departamentos_id'),
    ];
  }
}

?>