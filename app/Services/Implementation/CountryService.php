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
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $country = $this->model->find($id);
    if($country){
      $country->fecha_creado = Carbon::parse($country->created_at)->format('d-m-Y H:i:s');
      $country->fecha_modificado = Carbon::parse($country->updated_at)->format('d-m-Y H:i:s');
    }

    return $country;
  }

  public function create(array $data){
    $country = $this->model->create($data);
    if($country){
      $country->fecha_creado = Carbon::parse($country->created_at)->format('d-m-Y H:i:s');
    }

    return $country;
  }

  public function update(array $data, int $id){
    $country = $this->model->find($id);
    if($country){
      $country->fill($data);
      $country->save();
      $country->fecha_modificado = Carbon::parse($country->updated_at)->format('d-m-Y H:i:s');
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
        $country->fecha_eliminado = Carbon::parse($country->deleted_at)->format('d-m-Y H:i:s');
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