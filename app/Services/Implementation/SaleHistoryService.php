<?php

namespace App\Services\Implementation;

use App\Models\saleHistory;
use App\Services\Interfaces\ISaleHistory;
use Illuminate\Support\Carbon;

class saleHistoryService implements ISaleHistory{

  private $model;

  public function __construct()
  {
    $this->model = new saleHistory();
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
    $saleHistory = $this->model->find($id);
    if($saleHistory){
      $saleHistory->fecha_creado = Carbon::parse($saleHistory->created_at)->format('d-m-Y H:i:s');
      $saleHistory->fecha_modificado = Carbon::parse($saleHistory->updated_at)->format('d-m-Y H:i:s');
    }

    return $saleHistory;
  }

  public function create(array $data){
    $saleHistory = $this->model->create($data);
    if($saleHistory){
      $saleHistory->fecha_creado = Carbon::parse($saleHistory->created_at)->format('d-m-Y H:i:s');
    }

    return $saleHistory;
  }

  public function update(array $data, int $id){
    $saleHistory = $this->model->find($id);
    if($saleHistory){
      $saleHistory->fill($data);
      $saleHistory->save();
      $saleHistory->fecha_modificado = Carbon::parse($saleHistory->updated_at)->format('d-m-Y H:i:s');
      return $saleHistory;
    }

    return null;
  }

  public function delete(int $id){
    $saleHistory = $this->model->find($id);
    if($saleHistory != null){
      $saleHistory->estado = 0;
      $saleHistory->save();
      $result = $saleHistory->delete();
      if($result){
        $saleHistory->fecha_eliminado = Carbon::parse($saleHistory->deleted_at)->format('d-m-Y H:i:s');
        return $saleHistory;
      }
    }

    return false;
  }

  public function restore(int $id){
    $saleHistory = $this->model->withTrashed()->find($id);
    if($saleHistory != null && $saleHistory->trashed()){
      $saleHistory->estado = 1;
      $saleHistory->save();
      $result = $saleHistory->restore();
      if($result){
        return $saleHistory;
      }
    }

    return false;
  }

}

?>