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
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterByProvince(int $provinceId){
    $query = $this->model->select();
    if($provinceId){
      $query->where('provincias_id', $provinceId);
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
    $district = $this->model->create($data);
    if($district){
      $district->created_at = Carbon::parse($district->created_at)->format('Y-m-d H:i:s');
    }

    return $district;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $district = $this->model->find($id);
    if($district){
      $district->fill($data);
      $district->save();
      $district->updated_at = Carbon::parse($district->updated_at)->format('Y-m-d H:i:s');
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
        $district->deleted_st = Carbon::parse($district->deleted_at)->format('Y-m-d H:i:s');
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