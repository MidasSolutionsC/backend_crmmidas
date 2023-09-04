<?php

namespace App\Services\Implementation;

use App\Models\Ubigeo;
use App\Services\Interfaces\IUbigeo;
use Illuminate\Support\Carbon;

class UbigeoService implements IUbigeo{

  private $model;

  public function __construct()
  {
    $this->model = new Ubigeo();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function search(array $data){
    $search = $data['search'];
    $query = $this->model->select();
    $query->selectRaw("CONCAT(dpto, ', ', prov, ', ', distrito) as ciudad");
    $query->havingRaw("ciudad like ?", ['%' . $search . '%']);
    $query->take(20); // Limite de resultados
    $result = $query->get();
    return $result;
  }

  public function getById(string $ubigeo){
    $query = $this->model->select();
    $result = $query->find($ubigeo);
    return $result;
  }

  public function create(array $data){
    $ubigeo = $this->model->create($data);
    return $ubigeo;
  }

  public function update(array $data, string $ubigeo){
    $resUbigeo = $this->model->find($ubigeo);
    if($resUbigeo){
      $resUbigeo->fill($data);
      $resUbigeo->save();
      return $resUbigeo;
    }

    return null;
  }

  public function delete(string $ubigeo){
    $resUbigeo = $this->model->find($ubigeo);
    if($resUbigeo != null){
      $resUbigeo->save();
      $result = $resUbigeo->delete();
      if($result){
        return $resUbigeo;
      }
    }

    return false;
  }

  public function restore(string $ubigeo){
    $resUbigeo = $this->model->withTrashed()->find($ubigeo);
    if($resUbigeo != null && $resUbigeo->trashed()){
      $resUbigeo->save();
      $result = $resUbigeo->restore();
      if($result){
        return $resUbigeo;
      }
    }

    return false;
  }

}

?>