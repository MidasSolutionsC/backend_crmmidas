<?php

namespace App\Services\Implementation;

use App\Models\SaleDocument;
use App\Services\Interfaces\ISaleDocument;
use Illuminate\Support\Carbon;

class saleDocumentService implements ISaleDocument{

  private $model;

  public function __construct()
  {
    $this->model = new SaleDocument();
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
    $saleDocument = $this->model->find($id);
    if($saleDocument){
      $saleDocument->fecha_creado = Carbon::parse($saleDocument->created_at)->format('d-m-Y H:i:s');
      $saleDocument->fecha_modificado = Carbon::parse($saleDocument->updated_at)->format('d-m-Y H:i:s');
    }

    return $saleDocument;
  }

  public function create(array $data){
    $saleDocument = $this->model->create($data);
    if($saleDocument){
      $saleDocument->fecha_creado = Carbon::parse($saleDocument->created_at)->format('d-m-Y H:i:s');
    }

    return $saleDocument;
  }

  public function update(array $data, int $id){
    $saleDocument = $this->model->find($id);
    if($saleDocument){
      $saleDocument->fill($data);
      $saleDocument->save();
      $saleDocument->fecha_modificado = Carbon::parse($saleDocument->updated_at)->format('d-m-Y H:i:s');
      return $saleDocument;
    }

    return null;
  }

  public function delete(int $id){
    $saleDocument = $this->model->find($id);
    if($saleDocument != null){
      $saleDocument->estado = 0;
      $saleDocument->save();
      $result = $saleDocument->delete();
      if($result){
        $saleDocument->fecha_eliminado = Carbon::parse($saleDocument->deleted_at)->format('d-m-Y H:i:s');
        return $saleDocument;
      }
    }

    return false;
  }

  public function restore(int $id){
    $saleDocument = $this->model->withTrashed()->find($id);
    if($saleDocument != null && $saleDocument->trashed()){
      $saleDocument->estado = 1;
      $saleDocument->save();
      $result = $saleDocument->restore();
      if($result){
        return $saleDocument;
      }
    }

    return false;
  }

}

?>