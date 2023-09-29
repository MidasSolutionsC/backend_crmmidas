<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContactValidator{
  
  private $request;
  private $id;

  public function __construct(Request $request = null) {
    $this->request = $request;
    if ($request) {
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
      'empresas_id' => 'nullable|integer',
      'personas_id' => 'nullable|integer',
      'tipo' => 'required|string|size:3',
      'contacto' => [
        'required',
        'string',
        'max:60',
          Rule::unique('contactos', 'contacto')
              ->ignore($this->id, 'id')
              ->where(function ($query) {
                  $query->where('empresas_id', $this->request->input('empresas_id'))
                        ->orWhere('personas_id', $this->request->input('personas_id'));
              }),
      ],
      // 'contacto' => 'required|string|max:60|unique:contactos,contacto,' . $this->id . ',id,empresas_id,' . $this->request->input('empresas_id') . ','. $this->id . ',id,personas_id,' . $this->request->input('personas_id'),
      'is_primary' => 'nullable|boolean',
      'is_active' => 'nullable|boolean',
    ];
  }
}


?>