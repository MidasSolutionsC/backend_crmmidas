<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalendarValidator {
  
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
      'titulo' => 'required|string|max:50',
      'descripcion' => 'required|string',
      'fecha' => 'required|date:Y-m-d',
      'hora' => ['required', 'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'required|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
    ];
  }
}

?>