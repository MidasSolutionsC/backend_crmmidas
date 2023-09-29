<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BankAccountValidator {
  
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
      'clientes_id' => 'required|integer',
      'tipo_cuentas_bancarias_id' => 'required|integer',
      'cuenta' => [
        'required',
        'string',
        'max:30',
          Rule::unique('cuentas_bancarias', 'cuenta')
              ->ignore($this->id, 'id')
              ->where(function ($query) {
                  $query->where('clientes_id', $this->request->input('clientes_id'));
                  $query->where('tipo_cuentas_bancarias_id', $this->request->input('tipo_cuentas_bancarias_id'));
              }),
      ],
      // 'cuenta' => 'required|string|max:30|unique:cuentas_bancarias,cuenta,' . $this->id . ',id,clientes_id,' . $this->request->input('clientes_id'),
      'fecha_apertura' => 'nullable|date:Y-m-d',
      'is_primary' => 'nullable|boolean',
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'integer',
      'user_delete_id' => 'integer',
    ];
  }
}

?>