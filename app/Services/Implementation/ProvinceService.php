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
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }


  public function getFilterByDepartment(int $departmentId){
    $query = $this->model->select();
    if($departmentId){
      $query->where('departamentos_id', $departmentId);
    }
    
    $result = $query->get();

    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $province = $this->model->find($id);
    if($province){
      $province->fecha_creado = Carbon::parse($province->created_at)->format('d-m-Y H:i:s');
      $province->fecha_modificado = Carbon::parse($province->updated_at)->format('d-m-Y H:i:s');
    }

    return $province;
  }

  public function create(array $data){
    $province = $this->model->create($data);
    if($province){
      $province->fecha_creado = Carbon::parse($province->created_at)->format('d-m-Y H:i:s');
    }

    return $province;
  }

  public function update(array $data, int $id){
    $province = $this->model->find($id);
    if($province){
      $province->fill($data);
      $province->save();
      $province->fecha_modificado = Carbon::parse($province->updated_at)->format('d-m-Y H:i:s');
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
        $province->fecha_eliminado = Carbon::parse($province->deleted_at)->format('d-m-Y H:i:s');
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