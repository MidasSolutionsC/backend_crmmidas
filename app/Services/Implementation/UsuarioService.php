<?php

namespace App\Services\Implementation;

use App\Models\Usuario;
use App\Services\Interfaces\IUsuario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UsuarioService implements IUsuario {

  private $model;

  public function __construct() {
    $this->model = new Usuario();
  }

  public function login(array $data){
    $usuario = $this->model->where('correo', $data['correo'])->first();
    $result = [
      'login' => false,
      'message' => 'Usuario no encontrado'
    ];

    if(!is_null($usuario)){
      if(password_verify($data['clave'], $usuario->clave)){
        $apiToken = Str::random(250);
        $expiresAt  = Carbon::now()->addHours(12);
  
        $usuario->api_token = $apiToken;
        $usuario->expires_at = $expiresAt;
        $usuario->logueado = 1;
        $usuario->ultima_conexion = Carbon::now();
        $usuario->save();
  
        $result = [
          'login' => true,
          'message' => 'Bienvenido al sistema',
          'api_token' => $apiToken,
          'usuario' => $usuario
        ];
      } else {
        $result['message'] = 'Contraseña incorrecta';
      }
    } 

    return $result;
  }

  public function logout(int $id){
    $usuario = $this->model->find($id);
    $result = [
      'login' => true,
      'message' => 'Usuario no encontrado, no se pudo cerrar la sesión'
    ];

    if(!is_null($usuario)){

      $usuario->api_token = NULL;
      $usuario->expires_at = NULL;
      $usuario->logueado = 0;
      $usuario->ultima_conexion = Carbon::now();
      $usuario->save();

      $result = [
        'login' => false,
        'message' => 'Sesión cerrada, hasta pronto...'
      ];
    }
    
    return $result;
  }
  
  public function getAll(){
    $result = $this->model->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $usuario = $this->model->find($id);
    if($usuario){
      $usuario->fecha_creado = Carbon::parse($usuario->created_at)->format('d-m-Y H:i:s');
      $usuario->fecha_modificado = Carbon::parse($usuario->updated_at)->format('d-m-Y H:i:s');
    }

    return $usuario;
  }

  public function create(array $data){
    $data['clave'] = password_hash($data['clave'], PASSWORD_BCRYPT);
    $usuario = $this->model->create($data);
    if($usuario){
      $usuario->fecha_creado = Carbon::parse($usuario->created_at)->format('d-m-Y H:i:s');
    }

    return $usuario;
  }

  public function update(array $data, int $id){
    $usuario = $this->model->find($id);
    if($usuario){
      $data['clave'] = password_hash($data['clave'], PASSWORD_BCRYPT);
      $usuario->fill($data);
      $usuario->save();
      $usuario->fecha_modificado = Carbon::parse($usuario->updated_at)->format('d-m-Y H:i:s');
      return $usuario;
    } else {
      return ['message' => 'Error al actualizar los datos del usuario'];
    }
  }

  public function delete(int $id){
    $usuario = $this->model->find($id);
    if(!is_null($usuario)){
      $usuario->estado = 0;
      $usuario->save();
      $result = $usuario->delete();
      if($result){
        $usuario->fecha_eliminado = Carbon::parse($usuario->deleted_at)->format('d-m-Y H:i:s');
        return $usuario;
      }
    } else {
      return ['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.'];
    }
  }

  public function restore(int $id){
    $usuario = $this->model->withTrashed()->find($id);
    if(!is_null($usuario) && $usuario->trashed()){
      $usuario->estado = 1;
      $usuario->save();
      $result = $usuario->restore();
      if($result){
        return $usuario;
      }
    } else {
      return ['message' => 'El recurso solicitado ha sido restaurado previamente.'];
    }
  }
  
}

?>