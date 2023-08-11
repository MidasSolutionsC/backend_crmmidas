<?php

namespace App\Services\Implementation;

use App\Models\Service;
use App\Services\Interfaces\IService;
use Illuminate\Support\Carbon;

class ServiceService implements IService{

  private $model;

  public function __construct()
  {
    $this->model = new Service();
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
    $service = $this->model->find($id);
    if($service){
      $service->fecha_creado = Carbon::parse($service->created_at)->format('d-m-Y H:i:s');
      $service->fecha_modificado = Carbon::parse($service->updated_at)->format('d-m-Y H:i:s');
    }

    return $service;
  }

  public function create(array $data){
    $service = $this->model->create($data);
    if($service){
      $service->fecha_creado = Carbon::parse($service->created_at)->format('d-m-Y H:i:s');
    }

    return $service;
  }

  public function update(array $data, int $id){
    $service = $this->model->find($id);
    if($service){
      $service->fill($data);
      $service->save();
      $service->fecha_modificado = Carbon::parse($service->updated_at)->format('d-m-Y H:i:s');
      return $service;
    }

    return null;
  }

  public function delete(int $id){
    $service = $this->model->find($id);
    if($service != null){
      // $service->estado = 0;
      $service->save();
      $result = $service->delete();
      if($result){
        $service->fecha_eliminado = Carbon::parse($service->deleted_at)->format('d-m-Y H:i:s');
        return $service;
      }
    }

    return false;
  }

  public function restore(int $id){
    $service = $this->model->withTrashed()->find($id);
    if($service != null && $service->trashed()){
      // $service->estado = 1;
      $service->save();
      $result = $service->restore();
      if($result){
        return $service;
      }
    }

    return false;
  }

}

?>