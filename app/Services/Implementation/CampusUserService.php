<?php

namespace App\Services\Implementation;

use App\Models\CampusUser;
use App\Services\Interfaces\ICampusUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class CampusUserService implements ICampusUser{

  private $model;

  public function __construct()
  {
    $this->model = new CampusUser();
  }

  public function getAll(){
    $query = $this->model->select();
    
    // $query->addSelect(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS fecha_creado"));
    // $query->addSelect(DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS fecha_modificado"));
    
    $result = $query->get();
    return $result;
  }

  public function getFilterByCampus(int $campusId){
    $query = $this->model->select();
    if($campusId){
      $query->where('sedes_id', $campusId);
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
    $campusUser = $this->model->create($data);
    if($campusUser){
      $campusUser->created_at = Carbon::parse($campusUser->created_at)->format('Y-m-d H:i:s');
    }

    return $campusUser;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $campusUser = $this->model->find($id);
    if($campusUser){
      $campusUser->fill($data);
      $campusUser->save();
      $campusUser->updated_at = Carbon::parse($campusUser->updated_at)->format('Y-m-d H:i:s');
      return $campusUser;
    }

    return null;
  }

  public function delete(int $id){
    $campusUser = $this->model->find($id);
    if($campusUser != null){
      $campusUser->is_active = 0;
      $campusUser->save();
      $result = $campusUser->delete();
      if($result){
        $campusUser->deleted_st = Carbon::parse($campusUser->deleted_at)->format('Y-m-d H:i:s');
        return $campusUser;
      }
    }

    return false;
  }

  public function restore(int $id){
    $campusUser = $this->model->withTrashed()->find($id);
    if($campusUser != null && $campusUser->trashed()){
      $campusUser->is_active = 1;
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