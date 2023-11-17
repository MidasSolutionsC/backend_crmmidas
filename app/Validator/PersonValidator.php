<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    $identificaciones = $this->request->input('identificaciones');
    $rules = $this->rules();
    // Agregar reglas de validación para cada elemento en identificaciones
    if(!empty($identificaciones)){
      foreach ($identificaciones as $key => $identity) {
          $rules["identificaciones.*.id"] = 'nullable|integer';
          $rules["identificaciones.*.personas_id"] = 'nullable|integer';
          $rules["identificaciones.*.tipo_documentos_id"] = 'required|integer';
          $rules["identificaciones.*.documento"] = [
              'required',
              'string',
              'max:20',
              // Rule::unique('documentos_identificaciones', 'documento')
              //     ->ignore($this->id, 'personas_id')
              //     ->ignore($identity['id'] ?? null, 'id')
              //     // ->ignore( $identity['personas_id'], 'personas_id')
              //     ->where(function ($query) use ($identity) {
              //         $query->where('tipo_documentos_id', $identity['tipo_documentos_id']);
              //         $query->where(function ($subQuery) {
              //             $subQuery->where('personas_id', $this->id);
              //             $subQuery->whereNull('deleted_at');
              //         });
              //         $query->orWhere(function ($subQuery) {
              //             $subQuery->where('empresas_id', $this->request->input('empresas_id'));
              //             $subQuery->whereNull('deleted_at');
              //         });
              //     }),
          ];
      }
    }
    return Validator::make($this->request->all(), $rules);
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
      'identificaciones' => 'nullable|array',
      // 'identificaciones.*.id' => 'nullable|integer',
      // 'identificaciones.*.personas_id' => 'nullable|integer',
      // 'identificaciones.*.tipo_documentos_id' => 'required|integer',
      // 'identificaciones.*.documento' => 'required|string|max:20|unique:documentos_identificaciones,documento',
      // 'identificaciones.*.documento' => [
      //   'required',
      //   'string',
      //   'max:20',
      //   Rule::unique('documentos_identificaciones', 'documento')
      //     ->ignore($this->id, 'personas_id')
      //     // ->ignore($this->request->input('identificaciones.*.id'), 'id')
      //     ->where(function ($query) {
      //         $query->where('tipo_documentos_id', $this->request->input('identificaciones.*.tipo_documentos_id'));
      //         $query->where(function ($subQuery) {
      //             $subQuery->where('personas_id', $this->id);
      //             $subQuery->whereNull('deleted_at');
      //         });
      //         $query->orWhere(function ($subQuery) {
      //             $subQuery->where('empresas_id', $this->request->input('empresas_id'));
      //             $subQuery->whereNull('deleted_at');
      //         });
      //     }),
      // ],
    ];
  }
}

?>