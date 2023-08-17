<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeBankAccountValidator {

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
      'nombre' => 'required|string|max:50|unique:tipo_cuentas_bancarias,nombre,' . $this->id,
      'abreviacion' => 'required|string|max:15',
      'is_active' => 'nullable|boolean',
      'descripcion' => 'nullable|string',
    ];
  }
}


?>