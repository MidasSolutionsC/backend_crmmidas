<?php

namespace App\Services\Implementation;

use App\Models\Call;
use App\Services\Interfaces\ICall;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class CallService implements ICall{

  private $model;

  public function __construct()
  {
    $this->model = new Call();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterByUser(int $userId){
    $query = $this->model->select();
    if($userId){
      $query->where('user_create_id', $userId);
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
    $call = $this->model->create($data);
    if($call){
      $call->created_at = Carbon::parse($call->created_at)->format('Y-m-d H:i:s');
    }

    return $call;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $call = $this->model->find($id);
    if($call){
      $call->fill($data);
      $call->save();
      $call->updated_at = Carbon::parse($call->updated_at)->format('Y-m-d H:i:s');
      return $call;
    }

    return null;
  }

  public function delete(int $id){
    $call = $this->model->find($id);
    if($call != null){
      $call->is_active = 0;
      $call->save();
      $result = $call->delete();
      if($result){
        $call->deleted_st = Carbon::parse($call->deleted_at)->format('Y-m-d H:i:s');
        return $call;
      }
    }

    return false;
  }

  public function restore(int $id){
    $call = $this->model->withTrashed()->find($id);
    if($call != null && $call->trashed()){
      $call->is_active = 1;
      $call->save();
      $result = $call->restore();
      if($result){
        return $call;
      }
    }

    return false;
  }

}


?>