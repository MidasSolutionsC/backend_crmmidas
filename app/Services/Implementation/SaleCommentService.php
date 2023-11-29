<?php

namespace App\Services\Implementation;

use App\Models\SaleComment;
use App\Services\Interfaces\ISaleComment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class SaleCommentService implements ISaleComment{

  private $model;

  public function __construct()
  {
    $this->model = new SaleComment();
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

  public function getFilterBySaleDetail(int $saleDetailId){
    $query = $this->model->query();

    $query->with(['userCreate.person']);
    $query->select();

    if($saleDetailId){
      $query->where('ventas_detalles_id', $saleDetailId);
    }

    $result = $query->orderBy('created_at', 'desc')
      ->take(10) // Obtener los últimos 10 registros
      ->get();


    $newResult = collect($result)->sortBy('created_at')->values();
    return $newResult;
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

    $serviceComment = $this->model->create($data);
    if($serviceComment){
      $serviceComment->created_at = Carbon::parse($serviceComment->created_at)->format('Y-m-d H:i:s');
    }

    return $serviceComment;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }
    
    $serviceComment = $this->model->find($id);
    if($serviceComment){
      $serviceComment->fill($data);
      $serviceComment->save();
      $serviceComment->updated_at = Carbon::parse($serviceComment->updated_at)->format('Y-m-d H:i:s');
      return $serviceComment;
    }

    return null;
  }

  public function delete(int $id){
    $serviceComment = $this->model->find($id);
    if($serviceComment != null){
      $serviceComment->is_active = 0;
      $serviceComment->save();
      $result = $serviceComment->delete();
      if($result){
        $serviceComment->deleted_st = Carbon::parse($serviceComment->deleted_at)->format('Y-m-d H:i:s');
        return $serviceComment;
      }
    }

    return false;
  }

  public function restore(int $id){
    $serviceComment = $this->model->withTrashed()->find($id);
    if($serviceComment != null && $serviceComment->trashed()){
      $serviceComment->is_active = 1;
      $serviceComment->save();
      $result = $serviceComment->restore();
      if($result){
        return $serviceComment;
      }
    }

    return false;
  }

}


?>