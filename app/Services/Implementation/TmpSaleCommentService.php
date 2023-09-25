<?php

namespace App\Services\Implementation;

use App\Models\TmpSaleComment;
use App\Services\Interfaces\ISaleComment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class TmpSaleCommentService implements ISaleComment{

  private $model;

  public function __construct()
  {
    $this->model = new TmpSaleComment();
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

    $saleComment = $this->model->create($data);
    if($saleComment){
      $saleComment->created_at = Carbon::parse($saleComment->created_at)->format('Y-m-d H:i:s');
    }

    return $saleComment;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }

    $saleComment = $this->model->find($id);
    if($saleComment){
      $saleComment->fill($data);
      $saleComment->save();
      $saleComment->updated_at = Carbon::parse($saleComment->updated_at)->format('Y-m-d H:i:s');
      return $saleComment;
    }

    return null;
  }

  public function delete(int $id){
    $saleComment = $this->model->find($id);
    if($saleComment != null){
      $saleComment->is_active = 0;
      $saleComment->save();
      $result = $saleComment->delete();
      if($result){
        $saleComment->deleted_st = Carbon::parse($saleComment->deleted_at)->format('Y-m-d H:i:s');
        return $saleComment;
      }
    }

    return false;
  }

  public function restore(int $id){
    $saleComment = $this->model->withTrashed()->find($id);
    if($saleComment != null && $saleComment->trashed()){
      $saleComment->is_active = 1;
      $saleComment->save();
      $result = $saleComment->restore();
      if($result){
        return $saleComment;
      }
    }

    return false;
  }

}


?>