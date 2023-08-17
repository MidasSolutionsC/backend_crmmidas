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
    $saleDocument = $this->model->create($data);
    if($saleDocument){
      $saleDocument->created_at = Carbon::parse($saleDocument->created_at)->format('Y-m-d H:i:s');
    }

    return $saleDocument;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $saleDocument = $this->model->find($id);
    if($saleDocument){
      $saleDocument->fill($data);
      $saleDocument->save();
      $saleDocument->updated_at = Carbon::parse($saleDocument->updated_at)->format('Y-m-d H:i:s');
      return $saleDocument;
    }

    return null;
  }

  public function delete(int $id){
    $saleDocument = $this->model->find($id);
    if($saleDocument != null){
      $saleDocument->is_active = 0;
      $saleDocument->save();
      $result = $saleDocument->delete();
      if($result){
        $saleDocument->deleted_st = Carbon::parse($saleDocument->deleted_at)->format('Y-m-d H:i:s');
        return $saleDocument;
      }
    }

    return false;
  }

  public function restore(int $id){
    $saleDocument = $this->model->withTrashed()->find($id);
    if($saleDocument != null && $saleDocument->trashed()){
      $saleDocument->is_active = 1;
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