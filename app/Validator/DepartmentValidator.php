<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentValidator {
  
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
      'paises_id' => 'required|integer',
      'ubigeo_codigo' => 'required|string|size:2|unique:departamentos,ubigeo_codigo,'. $this->id,
      'nombre' => 'required|string|max:80|unique:departamentos,nombre,'. $this->id,
    ];
  }
}

?>