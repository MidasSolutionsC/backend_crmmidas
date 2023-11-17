<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SessionHistoryValidator {
  
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
      'usuarios_id' => 'required|integer',
      'dispositivo' => 'required|string|max:50',
      'descripcion' => 'required|string',
      'ip_address' => 'nullable|string|max:50',
      'so' => 'required|string|max:30',
      'navegador' => 'required|string|max:50',
      'login' => 'required|boolean',
      'tipo' => 'required|string|size:1',
      'fecha' => 'nullable|date:Y-m-d',
      'hora' => ['nullable', 'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
    ];
  }

}

?>