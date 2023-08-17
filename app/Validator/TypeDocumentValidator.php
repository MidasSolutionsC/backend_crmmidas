<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeDocumentValidator {

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
      'nombre' => 'required|string|max:40|unique:tipo_documentos,nombre,' . $this->id,
      'abreviacion' => 'required|string|max:15',
      'is_active' => 'nullable|boolean',
    ];
  }
}


?>