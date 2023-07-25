<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class UsuarioValidator {
  
  private $request;

  public function __construct(Request $request = null) {
    $this->request = $request;
  }

  public function validate(string $process = 'create'){
    if($process == 'create'){
      return Validator::make($this->request->all(), $this->rules(), $this->messages());
    }
    if($process == 'update'){
      return Validator::make($this->request->all(), $this->rulesUpdate(), $this->messages());
    }
    if($process == 'login'){
      return Validator::make($this->request->all(), $this->rulesLogin(), $this->messages());
    }
  }

  private function rules(){
    return [
      'tipo_usuarios_id' => 'required',
      'nombres' => 'required',
      'paterno' => 'required',
      'materno' => 'required',
      'tipo_documentos_id' => 'required',
      'documento' => 'required|unique:usuarios,documento,tipo_documentos_id,' . $this->request->input('id'),
      'correo' => 'required|email|unique:usuarios,correo,'. $this->request->input('id'),
      'clave' => 'required',
      'fecha_nacimiento' => 'required|date:Y-m-d',
    ];
  }

  private function rulesUpdate(){
    return [
      'tipo_usuarios_id' => 'required',
      'nombres' => 'required',
      'paterno' => 'required',
      'materno' => 'required',
      'tipo_documentos_id' => 'required',
      'documento' => 'required',
      'correo' => 'required|email',
      'clave' => 'required',
      'fecha_nacimiento' => 'required|date:Y-m-d',
    ];
  }

  private function rulesLogin(){
    return [
      'correo' => 'required|email',
      'clave' => 'required'
    ];
  }
  
  private function messages(){
    return [
      'tipo_usuarios_id.required' => 'El :attribute es requerido.',
      'nombres.required' => 'El :attribute es requerido.',
      'paterno.required' => 'El apellido :attribute es requerido.',
      'materno.required' => 'El apellido :attribute es requerido.',
      'tipo_documentos_id.required' => 'El :attribute es requerido.',
      'documento.required' => 'El :attribute es requerido.',
      'documento.unique' => 'El :attribute ya existe en la base de datos.',
      'correo.required' => 'El :attribute es requerido.',
      'correo.email' => 'El :attribute no es un email valido.',
      'correo.unique' => 'El :attribute ya existe en la base de datos.',
      'clave.required' => 'El :attribute es requerido.',
      'fecha_nacimiento.required' => 'La :attribute es requerido.',
      'fecha_nacimiento.date' => 'El formato de fecha de :attribute debe ser Y-m-d.',
    ];

  }
}

?>