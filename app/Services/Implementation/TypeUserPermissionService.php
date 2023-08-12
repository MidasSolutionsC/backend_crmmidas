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
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getFilterByTypeUser(int $typeUserId){
    $query = $this->model->select();
    if($typeUserId){
      $query->where('tipo_usuarios_id', $typeUserId);
    }
    
    $result = $query->get();

    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }


  public function getById(int $id){
    $typeUserPermission = $this->model->find($id);
    if($typeUserPermission){
      $typeUserPermission->fecha_creado = Carbon::parse($typeUserPermission->created_at)->format('d-m-Y H:i:s');
      $typeUserPermission->fecha_modificado = Carbon::parse($typeUserPermission->updated_at)->format('d-m-Y H:i:s');
    }

    return $typeUserPermission;
  }

  public function create(array $data){
    $typeUserPermission = $this->model->create($data);
    if($typeUserPermission){
      $typeUserPermission->fecha_creado = Carbon::parse($typeUserPermission->created_at)->format('d-m-Y H:i:s');
    }

    return $typeUserPermission;
  }

  public function update(array $data, int $id){
    $typeUserPermission = $this->model->find($id);
    if($typeUserPermission){
      $typeUserPermission->fill($data);
      $typeUserPermission->save();
      $typeUserPermission->fecha_modificado = Carbon::parse($typeUserPermission->updated_at)->format('d-m-Y H:i:s');
      return $typeUserPermission;
    }

    return null;
  }

  public function delete(int $id){
    $typeUserPermission = $this->model->find($id);
    if($typeUserPermission != null){
      $typeUserPermission->estado = 0;
      $typeUserPermission->save();
      $result = $typeUserPermission->delete();
      if($result){
        $typeUserPermission->fecha_eliminado = Carbon::parse($typeUserPermission->deleted_at)->format('d-m-Y H:i:s');
        return $typeUserPermission;
      }
    }

    return false;
  }

  public function restore(int $id){
    $typeUserPermission = $this->model->withTrashed()->find($id);
    if($typeUserPermission != null && $typeUserPermission->trashed()){
      $typeUserPermission->estado = 1;
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