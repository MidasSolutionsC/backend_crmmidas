<?php

namespace App\Services\Implementation;

use App\Models\Campus;
use App\Services\Interfaces\ICampus;
use Illuminate\Support\Carbon;

class CampusService implements ICampus{

  private $model;

  public function __construct()
  {
    $this->model = new Campus();
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
    $campus = $this->model->create($data);
    if($campus){
      $campus->created_at = Carbon::parse($campus->created_at)->format('Y-m-d H:i:s');
    }

    return $campus;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $campus = $this->model->find($id);
    if($campus){
      $campus->fill($data);
      $campus->save();
      $campus->updated_at = Carbon::parse($campus->updated_at)->format('Y-m-d H:i:s');
      return $campus;
    }

    return null;
  }

  public function delete(int $id){
    $campus = $this->model->find($id);
    if($campus != null){
      $campus->is_active = false;
      $campus->save();
      $result = $campus->delete();
      if($result){
        $campus->deleted_st = Carbon::parse($campus->deleted_at)->format('Y-m-d H:i:s');
        return $campus;
      }
    }

    return false;
  }

  public function restore(int $id){
    $campus = $this->model->withTrashed()->find($id);
    if($campus != null && $campus->trashed()){
      $campus->is_active = true;
      $campus->save();
      $result = $campus->restore();
      if($result){
        return $campus;
      }
    }

    return false;
  }

}


?>