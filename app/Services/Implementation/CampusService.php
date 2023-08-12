<?php

namespace App\Services\Implementation;

use App\Models\Campus;
use App\Services\Interfaces\ICampus;
use Illuminate\Support\Carbon;

class CampusService implements ICampus{

  private $model;

  public function __construct()
  {
    $this->model = new Campus();
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
    $campus = $this->model->find($id);
    if($campus){
      $campus->fecha_creado = Carbon::parse($campus->created_at)->format('d-m-Y H:i:s');
      $campus->fecha_modificado = Carbon::parse($campus->updated_at)->format('d-m-Y H:i:s');
    }

    return $campus;
  }

  public function create(array $data){
    $campus = $this->model->create($data);
    if($campus){
      $campus->fecha_creado = Carbon::parse($campus->created_at)->format('d-m-Y H:i:s');
    }

    return $campus;
  }

  public function update(array $data, int $id){
    $campus = $this->model->find($id);
    if($campus){
      $campus->fill($data);
      $campus->save();
      $campus->fecha_modificado = Carbon::parse($campus->updated_at)->format('d-m-Y H:i:s');
      return $campus;
    }

    return null;
  }

  public function delete(int $id){
    $campus = $this->model->find($id);
    if($campus != null){
      $campus->save();
      $result = $campus->delete();
      if($result){
        $campus->fecha_eliminado = Carbon::parse($campus->deleted_at)->format('d-m-Y H:i:s');
        return $campus;
      }
    }

    return false;
  }

  public function restore(int $id){
    $campus = $this->model->withTrashed()->find($id);
    if($campus != null && $campus->trashed()){
      $campus->save();
      $result = $campus->restore();
      if($result){
        return $campus;
      }
    }

    return false;
  }

}


?>