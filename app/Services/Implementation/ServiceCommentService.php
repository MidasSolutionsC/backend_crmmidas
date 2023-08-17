<?php

namespace App\Services\Implementation;

use App\Models\ServiceComment;
use App\Services\Interfaces\IServiceComment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class ServiceCommentService implements IServiceComment{

  private $model;

  public function __construct()
  {
    $this->model = new ServiceComment();
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
    $serviceComment = $this->model->create($data);
    if($serviceComment){
      $serviceComment->created_at = Carbon::parse($serviceComment->created_at)->format('Y-m-d H:i:s');
    }

    return $serviceComment;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $serviceComment = $this->model->find($id);
    if($serviceComment){
      $serviceComment->fill($data);
      $serviceComment->save();
      $serviceComment->updated_at = Carbon::parse($serviceComment->updated_at)->format('Y-m-d H:i:s');
      return $serviceComment;
    }

    return null;
  }

  public function delete(int $id){
    $serviceComment = $this->model->find($id);
    if($serviceComment != null){
      $serviceComment->is_active = 0;
      $serviceComment->save();
      $result = $serviceComment->delete();
      if($result){
        $serviceComment->deleted_st = Carbon::parse($serviceComment->deleted_at)->format('Y-m-d H:i:s');
        return $serviceComment;
      }
    }

    return false;
  }

  public function restore(int $id){
    $serviceComment = $this->model->withTrashed()->find($id);
    if($serviceComment != null && $serviceComment->trashed()){
      $serviceComment->is_active = 1;
      $serviceComment->save();
      $result = $serviceComment->restore();
      if($result){
        return $serviceComment;
      }
    }

    return false;
  }

}


?>