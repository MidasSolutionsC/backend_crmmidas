<?php

namespace App\Services\Implementation;

use App\Models\User;
use App\Services\Interfaces\IUser;
use Illuminate\Support\Carbon;

class UserService implements IUser {

  private $model;

  public function __construct() {
    $this->model = new User();
  }
  
  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }

  public function create(array $data){
    $data['created_at'] = Carbon::now(); 
    $data['clave'] = password_hash($data['clave'], PASSWORD_BCRYPT);
    $usuario = $this->model->create($data);
    if($usuario){
      $usuario->created_at = Carbon::parse($usuario->created_at)->format('Y-m-d H:i:s');
    }

    return $usuario;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $usuario = $this->model->find($id);
    if($usuario){
      $data['clave'] = password_hash($data['clave'], PASSWORD_BCRYPT);
      $usuario->fill($data);
      $usuario->save();
      $usuario->updated_at = Carbon::parse($usuario->updated_at)->format('Y-m-d H:i:s');
      return $usuario;
    } 
    
    return null;
  }

  public function delete(int $id){
    $usuario = $this->model->find($id);
    if(!is_null($usuario)){
      $usuario->is_active = 0;
      $usuario->save();
      $result = $usuario->delete();
      if($result){
        $usuario->deleted_st = Carbon::parse($usuario->deleted_at)->format('Y-m-d H:i:s');
        return $usuario;
      }
    } 
    
    return false;
  }

  public function restore(int $id){
    $usuario = $this->model->withTrashed()->find($id);
    if(!is_null($usuario) && $usuario->trashed()){
      $usuario->is_active = 1;
      $usuario->save();
      $result = $usuario->restore();
      if($result){
        return $usuario;
      }
    } 
    
    return false;
  }
  
}

?>