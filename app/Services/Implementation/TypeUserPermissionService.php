<?php

namespace App\Services\Implementation;

use App\Models\TypeUserPermission;
use App\Services\Interfaces\ITypeUserPermission;
use Illuminate\Support\Carbon;

class TypeUserPermissionService implements ITypeUserPermission{

  private $model;

  public function __construct()
  {
    $this->model = new TypeUserPermission();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterByTypeUser(int $typeUserId){
    $query = $this->model->select();
    if($typeUserId){
      $query->where('tipo_usuarios_id', $typeUserId);
    }

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
    $typeUserPermission = $this->model->create($data);
    if($typeUserPermission){
      $typeUserPermission->created_at = Carbon::parse($typeUserPermission->created_at)->format('Y-m-d H:i:s');
    }

    return $typeUserPermission;
  }

  public function update(array $data, int $id){
    $data['created_at'] = Carbon::now(); 
    $typeUserPermission = $this->model->find($id);
    if($typeUserPermission){
      $typeUserPermission->fill($data);
      $typeUserPermission->save();
      $typeUserPermission->updated_at = Carbon::parse($typeUserPermission->updated_at)->format('Y-m-d H:i:s');
      return $typeUserPermission;
    }

    return null;
  }

  public function delete(int $id){
    $typeUserPermission = $this->model->find($id);
    if($typeUserPermission != null){
      $typeUserPermission->is_active = 0;
      $typeUserPermission->save();
      $result = $typeUserPermission->delete();
      if($result){
        $typeUserPermission->deleted_st = Carbon::parse($typeUserPermission->deleted_at)->format('Y-m-d H:i:s');
        return $typeUserPermission;
      }
    }

    return false;
  }

  public function restore(int $id){
    $typeUserPermission = $this->model->withTrashed()->find($id);
    if($typeUserPermission != null && $typeUserPermission->trashed()){
      $typeUserPermission->is_active = 1;
      $typeUserPermission->save();
      $result = $typeUserPermission->restore();
      if($result){
        return $typeUserPermission;
      }
    }

    return false;
  }

}


?>