<?php

namespace App\Services\Implementation;

use App\Models\TypeUser;
use App\Services\Interfaces\ITypeUser;
use Illuminate\Support\Carbon;

class TypeUserService implements ITypeUser{

  private $model;

  public function __construct()
  {
    $this->model = new TypeUser();
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
    $tipoUsuario = $this->model->create($data);
    if($tipoUsuario){
      $tipoUsuario->created_at = Carbon::parse($tipoUsuario->created_at)->format('Y-m-d H:i:s');
    }

    return $tipoUsuario;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $tipoUsuario = $this->model->find($id);
    if($tipoUsuario){
      $tipoUsuario->fill($data);
      $tipoUsuario->save();
      $tipoUsuario->updated_at = Carbon::parse($tipoUsuario->updated_at)->format('Y-m-d H:i:s');
      return $tipoUsuario;
    }

    return null;
  }

  public function delete(int $id){
    $tipoUsuario = $this->model->find($id);
    if($tipoUsuario != null){
      $tipoUsuario->is_active = 0;
      $tipoUsuario->save();
      $result = $tipoUsuario->delete();
      if($result){
        $tipoUsuario->deleted_st = Carbon::parse($tipoUsuario->deleted_at)->format('Y-m-d H:i:s');
        return $tipoUsuario;
      }
    }

    return false;
  }

  public function restore(int $id){
    $tipoUsuario = $this->model->withTrashed()->find($id);
    if($tipoUsuario != null && $tipoUsuario->trashed()){
      $tipoUsuario->is_active = 1;
      $tipoUsuario->save();
      $result = $tipoUsuario->restore();
      if($result){
        return $tipoUsuario;
      }
    }

    return false;
  }
}

?>