<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IdentificationDocumentValidator {
  
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
      'personas_id' => 'nullable|integer',
      'empresas_id' => 'nullable|integer',
      'tipo_documentos_id' => 'required|integer',
      'documento' => [
        'required',
        'string',
        'max:20',
          Rule::unique('documentos_identificaciones', 'documento')
              ->ignore($this->id, 'id')
              ->where(function ($query) {
                // $query->where('personas_id', $this->request->input('personas_id'));
                // $query->where('tipo_documentos_id', $this->request->input('tipo_documentos_id'));
                $query->where(function ($subquery) {
                  $subquery->where('personas_id', $this->request->input('personas_id'))
                            ->where('tipo_documentos_id', $this->request->input('tipo_documentos_id'))
                            ->whereNull('deleted_at');
                })
                ->orWhere(function ($subquery) {
                    $subquery->where('empresas_id', $this->request->input('empresas_id'))
                             ->where('tipo_documentos_id', $this->request->input('tipo_documentos_id'))
                             ->whereNull('deleted_at');
                });
              })
      ],
      'reverso_documento' => 'nullable|string|max:250',
      'is_primary' => 'nullable|boolean',
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'nullable|integer',
      'user_update_id' => 'integer',
      'user_delete_id' => 'integer',
    ];
  }

}

?>