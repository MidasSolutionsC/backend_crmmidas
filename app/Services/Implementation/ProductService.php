<?php

namespace App\Services\Implementation;

use App\Models\Product;
use App\Services\Interfaces\IProduct;
use Illuminate\Support\Carbon;

class ProductService implements IProduct{

  private $model;

  public function __construct()
  {
    $this->model = new Product();
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
    $product = $this->model->find($id);
    if($product){
      $product->fecha_creado = Carbon::parse($product->created_at)->format('d-m-Y H:i:s');
      $product->fecha_modificado = Carbon::parse($product->updated_at)->format('d-m-Y H:i:s');
    }

    return $product;
  }

  public function create(array $data){
    $product = $this->model->create($data);
    if($product){
      $product->fecha_creado = Carbon::parse($product->created_at)->format('d-m-Y H:i:s');
    }

    return $product;
  }

  public function update(array $data, int $id){
    $product = $this->model->find($id);
    if($product){
      $product->fill($data);
      $product->save();
      $product->fecha_modificado = Carbon::parse($product->updated_at)->format('d-m-Y H:i:s');
      return $product;
    }

    return null;
  }

  public function delete(int $id){
    $product = $this->model->find($id);
    if($product != null){
      $product->estado = 0;
      $product->save();
      $result = $product->delete();
      if($result){
        $product->fecha_eliminado = Carbon::parse($product->deleted_at)->format('d-m-Y H:i:s');
        return $product;
      }
    }

    return false;
  }

  public function restore(int $id){
    $product = $this->model->withTrashed()->find($id);
    if($product != null && $product->trashed()){
      $product->estado = 1;
      $product->save();
      $result = $product->restore();
      if($result){
        return $product;
      }
    }

    return false;
  }

}


?>