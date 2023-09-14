<?php

namespace App\Services\Implementation;

use App\Models\SessionHistory;
use App\Services\Interfaces\ISessionHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class SessionHistoryService implements ISessionHistory{

  private $model;

  public function __construct()
  {
    $this->model = new SessionHistory();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterByUser(int $userId){
    $query = $this->model->select();
    if($userId){
      $query->where('usuarios_id', $userId);
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
    if(isset($data['user_auth_id'])){
      $data['user_create_id'] = $data['user_auth_id'];
    }
    $sessionHistory = $this->model->create($data);
    if($sessionHistory){
      $sessionHistory->created_at = Carbon::parse($sessionHistory->created_at)->format('Y-m-d H:i:s');
    }

    return $sessionHistory;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }
    $sessionHistory = $this->model->find($id);
    if($sessionHistory){
      $sessionHistory->fill($data);
      $sessionHistory->save();
      $sessionHistory->updated_at = Carbon::parse($sessionHistory->updated_at)->format('Y-m-d H:i:s');
      return $sessionHistory;
    }

    return null;
  }

  public function delete(int $id){
    $sessionHistory = $this->model->find($id);
    if($sessionHistory != null){
      $sessionHistory->save();
      $result = $sessionHistory->delete();
      if($result){
        $sessionHistory->deleted_st = Carbon::parse($sessionHistory->deleted_at)->format('Y-m-d H:i:s');
        return $sessionHistory;
      }
    }

    return false;
  }

  public function restore(int $id){
    $sessionHistory = $this->model->withTrashed()->find($id);
    if($sessionHistory != null && $sessionHistory->trashed()){
      $sessionHistory->save();
      $result = $sessionHistory->restore();
      if($result){
        return $sessionHistory;
      }
    }

    return false;
  }

}


?>