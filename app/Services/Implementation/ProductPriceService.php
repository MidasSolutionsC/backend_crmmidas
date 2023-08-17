<?php

namespace App\Services\Implementation;

use App\Models\ProductPrice;
use App\Services\Interfaces\IProductPrice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class ProductPriceService implements IProductPrice{

  private $model;

  public function __construct()
  {
    $this->model = new ProductPrice();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterByProduct(int $productId){
    $query = $this->model->select();
    if($productId){
      $query->where('productos_id', $productId);
    }
    
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
    $productPrice = $this->model->create($data);
    if($productPrice){
      $productPrice->created_at = Carbon::parse($productPrice->created_at)->format('Y-m-d H:i:s');
    }

    return $productPrice;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $productPrice = $this->model->find($id);
    if($productPrice){
      $productPrice->fill($data);
      $productPrice->save();
      $productPrice->updated_at = Carbon::parse($productPrice->updated_at)->format('Y-m-d H:i:s');
      return $productPrice;
    }

    return null;
  }

  public function delete(int $id){
    $productPrice = $this->model->find($id);
    if($productPrice != null){
      $productPrice->is_active = 0;
      $productPrice->save();
      $result = $productPrice->delete();
      if($result){
        $productPrice->deleted_st = Carbon::parse($productPrice->deleted_at)->format('Y-m-d H:i:s');
        return $productPrice;
      }
    }

    return false;
  }

  public function restore(int $id){
    $productPrice = $this->model->withTrashed()->find($id);
    if($productPrice != null && $productPrice->trashed()){
      $productPrice->is_active = 1;
      $productPrice->save();
      $result = $productPrice->restore();
      if($result){
        return $productPrice;
      }
    }

    return false;
  }

}


?>