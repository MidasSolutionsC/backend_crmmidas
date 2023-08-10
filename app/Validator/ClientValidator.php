<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientValidator {
  
  private $request;

  public function __construct(Request $request = null) {
    $this->request = $request;
  }

  public function validate(string $process = 'create'){
    if($process == 'create'){
      return Validator::make($this->request->all(), $this->rulesCreate(), $this->messages());
    }
    if($process == 'update'){
      return Validator::make($this->request->all(), $this->rulesUpdate(), $this->messages());
    }
  }

  private function rulesCreate(){
    return [
      'nombres' => 'required',
      'paterno' => 'required',
      'materno' => 'required',
      'correo' => 'required|email|unique:clientes,correo,'. $this->request->input('id'),
      'tipo_documentos_id' => 'required',
      'documento' => 'required|unique:clientes,documento,tipo_documentos_id,' . $this->request->input('id'),
      'fecha_nacimiento' => 'required|date:Y-m-d',
      'tipo_cliente' => 'required',
      'persona_juridica' => 'required',
      'codigo_postal' => 'required',
      'localidad' => 'required',
      'provincia' => 'required',
      'codigo_carga' => 'required',
      'territorial' => 'required',
      'telefono_principal' => 'required',
      'cta_bco' => 'required',
    ];
  }

  private function rulesUpdate(){
    return [
      'nombres' => 'required|string|max:60',
      'paterno' => 'required|string|max:60',
      'materno' => 'required|string|max:60',
      'correo' => 'required|email|unique:clientes,correo,'. $this->request->input('id'),
      'tipo_documentos_id' => 'required|integer',
      'documento' => 'required|unique:clientes,documento,tipo_documentos_id,' . $this->request->input('id'),
      'fecha_nacimiento' => 'required|date:Y-m-d',
      'tipo_cliente' => 'required',
      'persona_juridica' => 'required',
      'codigo_postal' => 'required',
      'localidad' => 'required',
      'provincia' => 'required',
      'codigo_carga' => 'required',
      'territorial' => 'required',
      'telefono_principal' => 'required',
      'cta_bco' => 'required',
    ];
  }
  
  private function messages(){
    return [
      'nombres.required' => 'El :attribute es requerido.',
      'paterno.required' => 'El apellido :attribute es requerido.',
      'materno.required' => 'El apellido :attribute es requerido.',
      'correo.required' => 'El :attribute es requerido.',
      'correo.email' => 'El :attribute no es un email valido.',
      'correo.unique' => 'El :attribute ya existe en la base de datos.',
      'tipo_documentos_id.required' => 'El :attribute es requerido.',
      'documento.required' => 'El :attribute es requerido.',
      'documento.unique' => 'El :attribute ya existe en la base de datos.',
      'fecha_nacimiento.required' => 'La :attribute es requerido.',
      'fecha_nacimiento.date' => 'El formato de fecha de :attribute debe ser Y-m-d.',
      'tipo_cliente.required' => 'La :attribute es requerido.',
      'persona_juridica.required' => 'La :attribute es requerido.',
      'codigo_postal.required' => 'La :attribute es requerido.',
      'localidad.required' => 'La :attribute es requerido.',
      'provincia.required' => 'La :attribute es requerido.',
      'codigo_carga.required' => 'La :attribute es requerido.',
      'territorial.required' => 'La :attribute es requerido.',
      'telefono_principal.required' => 'La :attribute es requerido.',
      'cta_bco.required' => 'La :attribute es requerido.',
    ];

  }
}

?>