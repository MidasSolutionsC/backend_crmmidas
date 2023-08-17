<?php

namespace App\Services\Implementation;

use App\Models\Departament;
use App\Services\Interfaces\IDepartment;
use Illuminate\Support\Carbon;

class DepartmentService implements IDepartment{

  private $model;

  public function __construct()
  {
    $this->model = new Departament();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterByCountry(int $countryId){
    $query = $this->model->select();
    if($countryId){
      $query->where('paises_id', $countryId);
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
    $department = $this->model->create($data);
    if($department){
      $department->created_at = Carbon::parse($department->created_at)->format('Y-m-d H:i:s');
    }

    return $department;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now();
    $department = $this->model->find($id);
    if($department){
      $department->fill($data);
      $department->save();
      $department->updated_at = Carbon::parse($department->updated_at)->format('Y-m-d H:i:s');
      return $department;
    }

    return null;
  }

  public function delete(int $id){
    $department = $this->model->find($id);
    if($department != null){
      $department->is_active = 0;
      $department->save();
      $result = $department->delete();
      if($result){
        $department->deleted_st = Carbon::parse($department->deleted_at)->format('Y-m-d H:i:s');
        return $department;
      }
    }

    return false;
  }

  public function restore(int $id){
    $department = $this->model->withTrashed()->find($id);
    if($department != null && $department->trashed()){
      $department->is_active = 1;
      $department->save();
      $result = $department->restore();
      if($result){
        return $department;
      }
    }

    return false;
  }

}


?>