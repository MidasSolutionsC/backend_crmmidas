<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PersonValidator {
  
  private $request;
  private $id;

  public function __construct(Request $request = null) {
    if ($request) {
      $this->request = $request;
      $this->id = $request->route('id');
    }
  }
  
  public function setRequest(array  $data, int $id = null){
    $this->request = new Request();
    $this->request->replace($data);
    $this->id = $id;
  }

  public function validate(){
    return Validator::make($this->request->all(), $this->rules());
  }

  private function rules(){
    return [
      'nombres' => 'required|string|max:60',
      'apellido_paterno' => 'required|string|max:60',
      'apellido_materno' => 'required|string|max:60',
      'nacionalidad' => 'nullable|string|max:80',
      'paises_id' => 'required|integer',
      'codigo_ubigeo' => 'nullable|string',
      // 'tipo_documentos_id' => 'required|integer',
      // 'documento' => 'required|string|max:11|unique:personas,documento,' . $this->id . ',id,deleted_at,NULL',
      // 'documento' => 'required|string|max:11|unique:personas,documento,' . $this->id . ',id,tipo_documentos_id,' . $this->request->input('tipo_documentos_id') . ',deleted_at,NULL',    
      // 'reverso_documento' => 'nullable|string|max:250',
      'fecha_nacimiento' => 'nullable|date:Y-m-d',
    ];
  }

}

?>