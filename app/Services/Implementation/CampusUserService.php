<?php

namespace App\Services\Implementation;

use App\Models\CampusUser;
use App\Services\Interfaces\ICampusUser;
use Illuminate\Support\Carbon;

class CampusUserService implements ICampusUser{

  private $model;

  public function __construct()
  {
    $this->model = new CampusUser();
  }

  public function getAll(){
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getFilterByCampus(int $campusId)
  {
    $query = $this->model->select();
    if($campusId){
      $query->where('sedes_id', $campusId);
    }
    
    $result = $query->get();

    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $campusUser = $this->model->find($id);
    if($campusUser){
      $campusUser->fecha_creado = Carbon::parse($campusUser->created_at)->format('d-m-Y H:i:s');
      $campusUser->fecha_modificado = Carbon::parse($campusUser->updated_at)->format('d-m-Y H:i:s');
    }

    return $campusUser;
  }

  public function create(array $data){
    $campusUser = $this->model->create($data);
    if($campusUser){
      $campusUser->fecha_creado = Carbon::parse($campusUser->created_at)->format('d-m-Y H:i:s');
    }

    return $campusUser;
  }

  public function update(array $data, int $id){
    $campusUser = $this->model->find($id);
    if($campusUser){
      $campusUser->fill($data);
      $campusUser->save();
      $campusUser->fecha_modificado = Carbon::parse($campusUser->updated_at)->format('d-m-Y H:i:s');
      return $campusUser;
    }

    return null;
  }

  public function delete(int $id){
    $campusUser = $this->model->find($id);
    if($campusUser != null){
      $campusUser->estado = 0;
      $campusUser->save();
      $result = $campusUser->delete();
      if($result){
        $campusUser->fecha_eliminado = Carbon::parse($campusUser->deleted_at)->format('d-m-Y H:i:s');
        return $campusUser;
      }
    }

    return false;
  }

  public function restore(int $id){
    $campusUser = $this->model->withTrashed()->find($id);
    if($campusUser != null && $campusUser->trashed()){
      $campusUser->estado = 1;
      $campusUser->save();
      $result = $campusUser->restore();
      if($result){
        return $campusUser;
      }
    }

    return false;
  }

}


?>