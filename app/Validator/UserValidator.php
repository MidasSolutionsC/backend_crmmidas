<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserValidator {
  
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

  public function validateNoPersonId(){
    return Validator::make($this->request->all(), $this->rulesNoPersonId());
  }

  private function rules(){
    return [
      'personas_id' => 'required|integer',
      'tipo_usuarios_id' => 'required|integer',
      'nombre_usuario' => 'required|string|max:20|unique:usuarios,nombre_usuario,'. $this->id,
      'clave' => 'nullable|string|max:100',
      'foto_perfil' => 'nullable|string|max:100',
      'api_token' => 'nullable|string|max:250',
      'expires_at' => 'nullable|integer',
      'session_activa' => 'nullable|boolean',
      'is_active' => 'nullable|boolean',
      'ultima_conexion' => 'date:Y-m-d H:i:s',
    ];
  }

  private function rulesNoPersonId(){
    return [
      'personas_id' => 'nullable|integer',
      'tipo_usuarios_id' => 'required|integer',
      'nombre_usuario' => 'required|string|max:20|unique:usuarios,nombre_usuario,'. $this->id,
      'clave' => 'nullable|string|max:100',
      'foto_perfil' => 'nullable|string|max:100',
      'api_token' => 'nullable|string|max:250',
      'expires_at' => 'nullable|integer',
      'session_activa' => 'nullable|boolean',
      'is_active' => 'nullable|boolean',
      'ultima_conexion' => 'date:Y-m-d H:i:s',
    ];
  }


  private function rulesCreateComplete(){
    return [
      'nombres' => 'required|string|max:60',
      'apellido_paterno' => 'required|string|max:60',
      'apellido_materno' => 'required|string|max:60',
      'paises_id' => 'required|integer',
      'tipo_documentos_id' => 'required|integer',
      // 'documento' => [
      //   'required',
      //   'string',
      //   'max:11',
      //   Rule::unique('personas')->where(function ($query) {
      //       return $query->where('tipo_documentos_id', $this->request->input('tipo_documentos_id'));
      //   }),
      // ],
      'documento' => 'required|string|max:11|unique:usuarios,documento,'. $this->id . ',id,tipo_documentos_id,'. $this->request->input('tipo_documentos_id'),
      'tipo_usuarios_id' => 'required|integer',
      'nombre_usuario' => 'required|string|max:20|unique:usuarios,nombre_usuario',
      'clave' => 'required',
    ];
  }
}

?>