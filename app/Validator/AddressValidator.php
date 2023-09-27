<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressValidator {
  
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
      'empresas_id' => 'nullable|integer',
      'personas_id' => 'nullable|integer',
      'domicilio' => 'nullable|string',
      'tipo' => 'required|string|max:30',
      'direccion' => 'required|string|max:250',
      'numero' => 'string|max:6',
      'escalera' => 'string|max:100',
      'portal' => 'string|max:100',
      'planta' => 'string|max:100',
      'puerta' => 'string|max:100',
      'codigo_postal' => 'required|max:5',
      'localidad' => 'string|max:90',
      'provincia' => 'string|max:90',
      'territorial' => 'string|max:90',
      'is_primary' => 'nullable|boolean',
      'is_active' => 'nullable|boolean',
    ];
  }
}

?>