<?php

namespace App\Services\Implementation;

use App\Models\District;
use App\Services\Interfaces\IDistrict;
use Illuminate\Support\Carbon;

class DistrictService implements IDistrict{

  private $model;

  public function __construct()
  {
    $this->model = new District();
  }

  public function getAll(){
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getFilterByProvince(int $provinceId){
    $query = $this->model->select();
    if($provinceId){
      $query->where('provincias_id', $provinceId);
    }
    
    $result = $query->get();

    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $district = $this->model->find($id);
    if($district){
      $district->fecha_creado = Carbon::parse($district->created_at)->format('d-m-Y H:i:s');
      $district->fecha_modificado = Carbon::parse($district->updated_at)->format('d-m-Y H:i:s');
    }

    return $district;
  }

  public function create(array $data){
    $district = $this->model->create($data);
    if($district){
      $district->fecha_creado = Carbon::parse($district->created_at)->format('d-m-Y H:i:s');
    }

    return $district;
  }

  public function update(array $data, int $id){
    $district = $this->model->find($id);
    if($district){
      $district->fill($data);
      $district->save();
      $district->fecha_modificado = Carbon::parse($district->updated_at)->format('d-m-Y H:i:s');
      return $district;
    }

    return null;
  }

  public function delete(int $id){
    $district = $this->model->find($id);
    if($district != null){
      $district->save();
      $result = $district->delete();
      if($result){
        $district->fecha_eliminado = Carbon::parse($district->deleted_at)->format('d-m-Y H:i:s');
        return $district;
      }
    }

    return false;
  }

  public function restore(int $id){
    $district = $this->model->withTrashed()->find($id);
    if($district != null && $district->trashed()){
      $district->save();
      $result = $district->restore();
      if($result){
        return $district;
      }
    }

    return false;
  }

}


?>