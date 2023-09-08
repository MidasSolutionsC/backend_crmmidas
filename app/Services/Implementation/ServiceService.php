<?php

namespace App\Services\Implementation;

use App\Models\Service;
use App\Services\Interfaces\IService;
use Illuminate\Support\Carbon;

class ServiceService implements IService{

  private $model;

  public function __construct()
  {
    $this->model = new Service();
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
    $data['user_create_id'] = $data['user_auth_id']; 
    $service = $this->model->create($data);
    if($service){
      $service->created_at = Carbon::parse($service->created_at)->format('Y-m-d H:i:s');
    }

    return $service;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id'];
    $service = $this->model->find($id);
    if($service){
      $service->fill($data);
      $service->save();
      $service->updated_at = Carbon::parse($service->updated_at)->format('Y-m-d H:i:s');
      return $service;
    }

    return null;
  }

  public function delete(int $id){
    $service = $this->model->find($id);
    if($service != null){
      $service->save();
      $result = $service->delete();
      if($result){
        $service->deleted_at = Carbon::parse($service->deleted_at)->format('Y-m-d H:i:s');
        return $service;
      }
    }

    return false;
  }

  public function restore(int $id){
    $service = $this->model->withTrashed()->find($id);
    if($service != null && $service->trashed()){
      $service->save();
      $result = $service->restore();
      if($result){
        return $service;
      }
    }

    return false;
  }

}

?>