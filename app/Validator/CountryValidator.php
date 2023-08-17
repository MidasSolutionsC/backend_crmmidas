<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryValidator {
  
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
      'iso_code' => 'required|string|size:2|unique:paises,iso_code,'. $this->id,
      'nombre' => 'required|string|max:255|unique:paises,nombre,'. $this->id,
    ];
  }
}

?>