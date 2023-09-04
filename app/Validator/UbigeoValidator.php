<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UbigeoValidator {
  
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
      'ubigeo' => 'required|string|max:6',
      'dpto' => 'required|string|max:32',
      'prov' => 'required|string|max:32',
      'distrito' => 'required|string|max:32',
      'ubigeo2' => 'required|string|max:6',
      'orden' => 'nullable|string|max:1',
    ];
  }
}

?>