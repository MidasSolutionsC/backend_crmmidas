<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberValidator {

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
      'grupos_id' => 'required|integer',
      'usuarios_id' => 'required|integer|unique:integrantes,usuarios_id,'. $this->id . ',id,grupos_id,' . $this->request->input('grupos_id'),
      'is_active' => 'nullable|boolean',
    ];
  }

}


?>