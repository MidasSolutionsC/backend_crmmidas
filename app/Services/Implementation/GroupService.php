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
    $query = $this->model->select(
      'grupos.*',
      'SD.nombre as sedes_nombre'
    )->join('sedes as SD', 'grupos.sedes_id', 'SD.id');

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
    $group = $this->model->create($data);
    if($group){
      $group->created_at = Carbon::parse($group->created_at)->format('Y-m-d H:i:s');
      $group->sedes_nombre = $group->campus->nombre;
    }

    return $group;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $group = $this->model->find($id);
    if($group){
      $group->fill($data);
      $group->save();
      $group->updated_at = Carbon::parse($group->updated_at)->format('Y-m-d H:i:s');
      $group->sedes_nombre = $group->campus->nombre;
      return $group;
    }

    return null;
  }

  public function delete(int $id){
    $group = $this->model->find($id);
    if($group != null){
      $group->is_active = 0;
      $group->save();
      $result = $group->delete();
      if($result){
        $group->deleted_st = Carbon::parse($group->deleted_at)->format('Y-m-d H:i:s');
        return $group;
      }
    }

    return false;
  }

  public function restore(int $id){
    $group = $this->model->withTrashed()->find($id);
    if($group != null && $group->trashed()){
      $group->is_active = 1;
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