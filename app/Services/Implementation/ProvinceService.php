<?php

namespace App\Services\Implementation;

use App\Models\Province;
use App\Services\Interfaces\IProvince;
use Illuminate\Support\Carbon;

class ProvinceService implements IProvince{

  private $model;

  public function __construct()
  {
    $this->model = new Province();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }


  public function getFilterByDepartment(int $departmentId){
    $query = $this->model->select();
    if($departmentId){
      $query->where('departamentos_id', $departmentId);
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
    $province = $this->model->create($data);
    if($province){
      $province->created_at = Carbon::parse($province->created_at)->format('Y-m-d H:i:s');
    }

    return $province;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $province = $this->model->find($id);
    if($province){
      $province->fill($data);
      $province->save();
      $province->updated_at = Carbon::parse($province->updated_at)->format('Y-m-d H:i:s');
      return $province;
    }

    return null;
  }

  public function delete(int $id){
    $province = $this->model->find($id);
    if($province != null){
      $province->save();
      $result = $province->delete();
      if($result){
        $province->deleted_st = Carbon::parse($province->deleted_at)->format('Y-m-d H:i:s');
        return $province;
      }
    }

    return false;
  }

  public function restore(int $id){
    $province = $this->model->withTrashed()->find($id);
    if($province != null && $province->trashed()){
      $province->save();
      $result = $province->restore();
      if($result){
        return $province;
      }
    }

    return false;
  }

}


?>