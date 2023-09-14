<?php

namespace App\Services\Implementation;

use App\Models\TmpSaleDetail;
use App\Services\Interfaces\ISaleDetail;
use Illuminate\Support\Carbon;

class TmpSaleDetailService implements ISaleDetail{

  private $model;

  public function __construct()
  {
    $this->model = new TmpSaleDetail();
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
    if(isset($data['user_auth_id'])){
      $data['user_create_id'] = $data['user_auth_id'];
    }

    $saleDetail = $this->model->create($data);
    if($saleDetail){
      $saleDetail->created_at = Carbon::parse($saleDetail->created_at)->format('Y-m-d H:i:s');
    }

    return $saleDetail;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }
    
    $saleDetail = $this->model->find($id);
    if($saleDetail){
      $saleDetail->fill($data);
      $saleDetail->save();
      $saleDetail->updated_at = Carbon::parse($saleDetail->updated_at)->format('Y-m-d H:i:s');
      return $saleDetail;
    }

    return null;
  }

  public function delete(int $id){
    $saleDetail = $this->model->find($id);
    if($saleDetail != null){
      $saleDetail->save();
      $result = $saleDetail->delete();
      if($result){
        $saleDetail->deleted_st = Carbon::parse($saleDetail->deleted_at)->format('Y-m-d H:i:s');
        return $saleDetail;
      }
    }

    return false;
  }

  public function restore(int $id){
    $saleDetail = $this->model->withTrashed()->find($id);
    if($saleDetail != null && $saleDetail->trashed()){
      $saleDetail->save();
      $result = $saleDetail->restore();
      if($result){
        return $saleDetail;
      }
    }

    return false;
  }

}


?>