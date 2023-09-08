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
    $data['user_create_id'] = $data['user_auth_id'];
    $sale = $this->model->create($data);
    if($sale){
      $sale->created_at = Carbon::parse($sale->created_at)->format('Y-m-d H:i:s');
    }

    return $sale;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id'];
    $sale = $this->model->find($id);
    if($sale){
      $sale->fill($data);
      $sale->save();
      $sale->updated_at = Carbon::parse($sale->updated_at)->format('Y-m-d H:i:s');
      return $sale;
    }

    return null;
  }

  public function delete(int $id){
    $sale = $this->model->find($id);
    if($sale != null){
      $sale->is_active = 0;
      $sale->save();
      $result = $sale->delete();
      if($result){
        $sale->deleted_st = Carbon::parse($sale->deleted_at)->format('Y-m-d H:i:s');
        return $sale;
      }
    }

    return false;
  }

  public function restore(int $id){
    $sale = $this->model->withTrashed()->find($id);
    if($sale != null && $sale->trashed()){
      $sale->is_active = 1;
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