<?php

namespace App\Services\Implementation;

use App\Models\ServiceHistory;
use App\Services\Interfaces\IServiceHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class ServiceHistoryService implements IServiceHistory{

  private $model;

  public function __construct()
  {
    $this->model = new ServiceHistory();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterByService(int $serviceId){
    $query = $this->model->select();
    if($serviceId){
      $query->where('servicios_id', $serviceId);
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
    $data['user_create_id'] = $data['user_auth_id'];
    $serviceHistory = $this->model->create($data);
    if($serviceHistory){
      $serviceHistory->created_at = Carbon::parse($serviceHistory->created_at)->format('Y-m-d H:i:s');
    }

    return $serviceHistory;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id'];
    $serviceHistory = $this->model->find($id);
    if($serviceHistory){
      $serviceHistory->fill($data);
      $serviceHistory->save();
      $serviceHistory->updated_at = Carbon::parse($serviceHistory->updated_at)->format('Y-m-d H:i:s');
      return $serviceHistory;
    }

    return null;
  }

  public function delete(int $id){
    $serviceHistory = $this->model->find($id);
    if($serviceHistory != null){
      $serviceHistory->is_active = 0;
      $serviceHistory->save();
      $result = $serviceHistory->delete();
      if($result){
        $serviceHistory->deleted_st = Carbon::parse($serviceHistory->deleted_at)->format('Y-m-d H:i:s');
        return $serviceHistory;
      }
    }

    return false;
  }

  public function restore(int $id){
    $serviceHistory = $this->model->withTrashed()->find($id);
    if($serviceHistory != null && $serviceHistory->trashed()){
      $serviceHistory->is_active = 1;
      $serviceHistory->save();
      $result = $serviceHistory->restore();
      if($result){
        return $serviceHistory;
      }
    }

    return false;
  }

}


?>