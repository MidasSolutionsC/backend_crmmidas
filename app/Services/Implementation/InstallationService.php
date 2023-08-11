<?php

namespace App\Services\Implementation;

use App\Models\Installation;
use App\Services\Interfaces\IInstallation;
use Illuminate\Support\Carbon;

class InstallationService implements IInstallation{

  private $model;

  public function __construct()
  {
    $this->model = new Installation();
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
    $installation = $this->model->find($id);
    if($installation){
      $installation->fecha_creado = Carbon::parse($installation->created_at)->format('d-m-Y H:i:s');
      $installation->fecha_modificado = Carbon::parse($installation->updated_at)->format('d-m-Y H:i:s');
    }

    return $installation;
  }

  public function create(array $data){
    $installation = $this->model->create($data);
    if($installation){
      $installation->fecha_creado = Carbon::parse($installation->created_at)->format('d-m-Y H:i:s');
    }

    return $installation;
  }

  public function update(array $data, int $id){
    $installation = $this->model->find($id);
    if($installation){
      $installation->fill($data);
      $installation->save();
      $installation->fecha_modificado = Carbon::parse($installation->updated_at)->format('d-m-Y H:i:s');
      return $installation;
    }

    return null;
  }

  public function delete(int $id){
    $installation = $this->model->find($id);
    if($installation != null){
      $installation->estado = 0;
      $installation->save();
      $result = $installation->delete();
      if($result){
        $installation->fecha_eliminado = Carbon::parse($installation->deleted_at)->format('d-m-Y H:i:s');
        return $installation;
      }
    }

    return false;
  }

  public function restore(int $id){
    $installation = $this->model->withTrashed()->find($id);
    if($installation != null && $installation->trashed()){
      $installation->estado = 1;
      $installation->save();
      $result = $installation->restore();
      if($result){
        return $installation;
      }
    }

    return false;
  }

}

?>