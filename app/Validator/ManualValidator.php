<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManualValidator {
  
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
      'nombre' => 'required|string|max:50',
      'tipo' => 'required|string|size:1|in:S,B,M,R,O',
      'archivo' => 'nullable|string|max:100',
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'integer',
      'user_delete_id' => 'integer',
      'file' => 'nullable|file|max:10240', // 10 megabytes.
    ];
  }
}

?>