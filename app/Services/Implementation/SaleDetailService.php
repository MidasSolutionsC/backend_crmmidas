<?php

namespace App\Services\Implementation;

use App\Models\SaleDetail;
use App\Services\Interfaces\ISaleDetail;
use Illuminate\Support\Carbon;

class SaleDetailService implements ISaleDetail{

  private $model;

  public function __construct()
  {
    $this->model = new SaleDetail();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }


  public function getFilterBySale(int $saleId){
    $query = $this->model->select();
    if($saleId){
      $query->where('ventas_id', $saleId);
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
    $data['user_create_id'] = $data['user_auth_id'];
    $saledetail = $this->model->create($data);
    if($saledetail){
      $saledetail->created_at = Carbon::parse($saledetail->created_at)->format('Y-m-d H:i:s');
    }

    return $saledetail;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id'];
    $saledetail = $this->model->find($id);
    if($saledetail){
      $saledetail->fill($data);
      $saledetail->save();
      $saledetail->updated_at = Carbon::parse($saledetail->updated_at)->format('Y-m-d H:i:s');
      return $saledetail;
    }

    return null;
  }

  public function delete(int $id){
    $saledetail = $this->model->find($id);
    if($saledetail != null){
      $saledetail->save();
      $result = $saledetail->delete();
      if($result){
        $saledetail->deleted_st = Carbon::parse($saledetail->deleted_at)->format('Y-m-d H:i:s');
        return $saledetail;
      }
    }

    return false;
  }

  public function restore(int $id){
    $saledetail = $this->model->withTrashed()->find($id);
    if($saledetail != null && $saledetail->trashed()){
      $saledetail->save();
      $result = $saledetail->restore();
      if($result){
        return $saledetail;
      }
    }

    return false;
  }

}


?>