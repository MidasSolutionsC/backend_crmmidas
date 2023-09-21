<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyValidator {
  
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
      'paises_id' => 'required|integer',
      'codigo_ubigeo' => 'nullable|string',
      'razon_social' => 'required|string|max:80',
      'nombre_comercial' => 'nullable|string|max:80',
      'descripcion' => 'nullable|string',
      'tipo_documentos_id' => 'required|integer',
      'documento' => 'required|string|max:11|unique:empresas,documento,'. $this->id . ',id,tipo_documentos_id,'. $this->request->input('tipo_documentos_id'),
      'tipo_empresa' => 'nullable|string|max:30',
      // 'direccion' => 'nullable|string|max:250',
      // 'ciudad' => 'nullable|string|max:60',
      // 'telefono' => 'nullable|string|max:11',
      'correo' => 'required|max:100|email|unique:empresas,correo,' . $this->id . ',id',
      'is_active' => 'nullable|is_active',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
    ];
  }
}

?>