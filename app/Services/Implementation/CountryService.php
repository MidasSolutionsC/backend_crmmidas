<?php

namespace App\Services\Implementation;

use App\Models\Country;
use App\Services\Interfaces\ICountry;
use Illuminate\Support\Carbon;

class CountryService implements ICountry{

  private $model;

  public function __construct()
  {
    $this->model = new Country();
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
    $country = $this->model->create($data);
    if($country){
      $country->created_at = Carbon::parse($country->created_at)->format('Y-m-d H:i:s');
    }

    return $country;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now();
    $country = $this->model->find($id);
    if($country){
      $country->fill($data);
      $country->save();
      $country->updated_at = Carbon::parse($country->updated_at)->format('Y-m-d H:i:s');
      return $country;
    }

    return null;
  }

  public function delete(int $id){
    $country = $this->model->find($id);
    if($country != null){
      $country->save();
      $result = $country->delete();
      if($result){
        $country->deleted_st = Carbon::parse($country->deleted_at)->format('Y-m-d H:i:s');
        return $country;
      }
    }

    return false;
  }

  public function restore(int $id){
    $country = $this->model->withTrashed()->find($id);
    if($country != null && $country->trashed()){
      $country->save();
      $result = $country->restore();
      if($result){
        return $country;
      }
    }

    return false;
  }

}

?>