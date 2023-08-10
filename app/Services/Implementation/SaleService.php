<?php

namespace App\Services\Implementation;

use App\Models\Sale;
use App\Services\Interfaces\ISale;
use Illuminate\Support\Carbon;

class SaleService implements ISale{

  private $model;

  public function __construct()
  {
    $this->model = new Sale();
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
    $sale = $this->model->find($id);
    if($sale){
      $sale->fecha_creado = Carbon::parse($sale->created_at)->format('d-m-Y H:i:s');
      $sale->fecha_modificado = Carbon::parse($sale->updated_at)->format('d-m-Y H:i:s');
    }

    return $sale;
  }

  public function create(array $data){
    $sale = $this->model->create($data);
    if($sale){
      $sale->fecha_creado = Carbon::parse($sale->created_at)->format('d-m-Y H:i:s');
    }

    return $sale;
  }

  public function update(array $data, int $id){
    $sale = $this->model->find($id);
    if($sale){
      $sale->fill($data);
      $sale->save();
      $sale->fecha_modificado = Carbon::parse($sale->updated_at)->format('d-m-Y H:i:s');
      return $sale;
    }

    return null;
  }

  public function delete(int $id){
    $sale = $this->model->find($id);
    if($sale != null){
      $sale->estado = 0;
      $sale->save();
      $result = $sale->delete();
      if($result){
        $sale->fecha_eliminado = Carbon::parse($sale->deleted_at)->format('d-m-Y H:i:s');
        return $sale;
      }
    }

    return false;
  }

  public function restore(int $id){
    $sale = $this->model->withTrashed()->find($id);
    if($sale != null && $sale->trashed()){
      $sale->estado = 1;
      $sale->save();
      $result = $sale->restore();
      if($result){
        return $sale;
      }
    }

    return false;
  }

}

?>