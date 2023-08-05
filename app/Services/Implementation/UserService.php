<?php

namespace App\Services\Implementation;

use App\Models\Usuario;
use App\Services\Interfaces\IUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserService implements IUser {

  private $model;

  public function __construct() {
    $this->model = new Usuario();
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