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
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getFilterByCountry(int $countryId){
    $query = $this->model->select();
    if($countryId){
      $query->where('paises_id', $countryId);
    }
    
    $result = $query->get();

    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $department = $this->model->find($id);
    if($department){
      $department->fecha_creado = Carbon::parse($department->created_at)->format('d-m-Y H:i:s');
      $department->fecha_modificado = Carbon::parse($department->updated_at)->format('d-m-Y H:i:s');
    }

    return $department;
  }

  public function create(array $data){
    $department = $this->model->create($data);
    if($department){
      $department->fecha_creado = Carbon::parse($department->created_at)->format('d-m-Y H:i:s');
    }

    return $department;
  }

  public function update(array $data, int $id){
    $department = $this->model->find($id);
    if($department){
      $department->fill($data);
      $department->save();
      $department->fecha_modificado = Carbon::parse($department->updated_at)->format('d-m-Y H:i:s');
      return $department;
    }

    return null;
  }

  public function delete(int $id){
    $department = $this->model->find($id);
    if($department != null){
      $department->estado = 0;
      $department->save();
      $result = $department->delete();
      if($result){
        $department->fecha_eliminado = Carbon::parse($department->deleted_at)->format('d-m-Y H:i:s');
        return $department;
      }
    }

    return false;
  }

  public function restore(int $id){
    $department = $this->model->withTrashed()->find($id);
    if($department != null && $department->trashed()){
      $department->estado = 1;
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