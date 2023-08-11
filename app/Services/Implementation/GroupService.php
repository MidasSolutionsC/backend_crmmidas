<?php

namespace App\Services\Implementation;

use App\Models\Group;
use App\Services\Interfaces\IGroup;
use Illuminate\Support\Carbon;

class GroupService implements IGroup{

  private $model;

  public function __construct()
  {
    $this->model = new Group();
  }

  public function getAll(){
    $result = $this->model->select('id', 'nombre', 'descripcion', 'estado')->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $group = $this->model->find($id);
    if($group){
      $group->fecha_creado = Carbon::parse($group->created_at)->format('d-m-Y H:i:s');
      $group->fecha_modificado = Carbon::parse($group->updated_at)->format('d-m-Y H:i:s');
    }

    return $group;
  }

  public function create(array $data){
    $group = $this->model->create($data);
    if($group){
      $group->fecha_creado = Carbon::parse($group->created_at)->format('d-m-Y H:i:s');
    }

    return $group;
  }

  public function update(array $data, int $id){
    $group = $this->model->find($id);
    if($group){
      $group->fill($data);
      $group->save();
      $group->fecha_modificado = Carbon::parse($group->updated_at)->format('d-m-Y H:i:s');
      return $group;
    }

    return null;
  }

  public function delete(int $id){
    $group = $this->model->find($id);
    if($group != null){
      $group->estado = 0;
      $group->save();
      $result = $group->delete();
      if($result){
        $group->fecha_eliminado = Carbon::parse($group->deleted_at)->format('d-m-Y H:i:s');
        return $group;
      }
    }

    return false;
  }

  public function restore(int $id){
    $group = $this->model->withTrashed()->find($id);
    if($group != null && $group->trashed()){
      $group->estado = 1;
      $group->save();
      $result = $group->restore();
      if($result){
        return $group;
      }
    }

    return false;
  }

}


?>