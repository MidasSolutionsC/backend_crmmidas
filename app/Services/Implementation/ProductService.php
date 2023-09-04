<?php

namespace App\Services\Implementation;

use App\Models\Product;
use App\Services\Interfaces\IProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductService implements IProduct{

  private $model;

  public function __construct()
  {
    $this->model = new Product();
  }

  public function getAll(){
    // $query = $this->model->select();
    $query = $this->model->select('productos.*', 'tipo_servicios.nombre as tipo_servicios_nombre', 'pp.precio as precio')
      ->join('tipo_servicios', 'productos.tipo_servicios_id', '=', 'tipo_servicios.id')
      ->leftJoin('productos_precios as pp', function ($join) {
          $join->on('productos.id', '=', 'pp.productos_id')
              ->where('pp.created_at', '=', function ($subquery) {
                  $subquery->select(DB::raw('MAX(created_at)'))
                      ->from('productos_precios')
                      ->whereColumn('productos_id', 'productos.id');
              });
      });
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
    $product = $this->model->create($data);
    if($product){
      $product->created_at = Carbon::parse($product->created_at)->format('Y-m-d H:i:s');
    }

    return $product;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $product = $this->model->find($id);
    if($product){
      $product->fill($data);
      $product->save();
      $product->updated_at = Carbon::parse($product->updated_at)->format('Y-m-d H:i:s');
      return $product;
    }

    return null;
  }

  public function delete(int $id){
    $product = $this->model->find($id);
    if($product != null){
      $product->is_active = 0;
      $product->save();
      $result = $product->delete();
      if($result){
        $product->deleted_st = Carbon::parse($product->deleted_at)->format('Y-m-d H:i:s');
        return $product;
      }
    }

    return false;
  }

  public function restore(int $id){
    $product = $this->model->withTrashed()->find($id);
    if($product != null && $product->trashed()){
      $product->is_active = 1;
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