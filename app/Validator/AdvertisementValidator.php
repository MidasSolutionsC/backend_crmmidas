<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvertisementValidator {
  
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
      'descripcion' => 'nullable|string|max:550',
      'tipo' => 'required|string|in:I,E',
      'imagen' => 'nullable|string|max:100',
      'file' => 'nullable|file|max:102400', // 100 megabytes.
      'order' => 'nullable|integer',
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'integer',
      'user_delete_id' => 'integer',
    ];
  }
}

?>