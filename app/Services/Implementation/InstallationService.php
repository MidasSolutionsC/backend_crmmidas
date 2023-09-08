<?php

namespace App\Services\Implementation;

use App\Models\Installation;
use App\Services\Interfaces\IInstallation;
use Illuminate\Support\Carbon;

class InstallationService implements IInstallation{

  private $model;

  public function __construct()
  {
    $this->model = new Installation();
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
    $installation = $this->model->create($data);
    if($installation){
      $installation->created_at = Carbon::parse($installation->created_at)->format('Y-m-d H:i:s');
    }

    return $installation;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id'];
    $installation = $this->model->find($id);
    if($installation){
      $installation->fill($data);
      $installation->save();
      $installation->updated_at = Carbon::parse($installation->updated_at)->format('Y-m-d H:i:s');
      return $installation;
    }

    return null;
  }

  public function delete(int $id){
    $installation = $this->model->find($id);
    if($installation != null){
      $installation->is_active = 0;
      $installation->save();
      $result = $installation->delete();
      if($result){
        $installation->deleted_st = Carbon::parse($installation->deleted_at)->format('Y-m-d H:i:s');
        return $installation;
      }
    }

    return false;
  }

  public function restore(int $id){
    $installation = $this->model->withTrashed()->find($id);
    if($installation != null && $installation->trashed()){
      $installation->is_active = 1;
      $installation->save();
      $result = $installation->restore();
      if($result){
        return $installation;
      }
    }

    return false;
  }

}

?>