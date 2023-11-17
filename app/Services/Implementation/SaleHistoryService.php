<?php

namespace App\Services\Implementation;

use App\Models\SaleHistory;
use App\Services\Interfaces\ISaleHistory;
use Illuminate\Support\Carbon;

class SaleHistoryService implements ISaleHistory{

  private $model;

  public function __construct()
  {
    $this->model = new SaleHistory();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterBySale(int $saleId){
    $query = $this->model->select();

    $query->select(
      'ventas_historial.*',
      'TE.nombre as tipo_estados_nombre', 
    );

    $query->join('tipo_estados as TE', 'ventas_historial.tipo_estados_id', 'TE.id');

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

    $saleHistory = $this->model->create($data);
    if($saleHistory){
      $saleHistory->created_at = Carbon::parse($saleHistory->created_at)->format('Y-m-d H:i:s');
    }

    return $saleHistory;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }

    $saleHistory = $this->model->find($id);
    if($saleHistory){
      $saleHistory->fill($data);
      $saleHistory->save();
      $saleHistory->updated_at = Carbon::parse($saleHistory->updated_at)->format('Y-m-d H:i:s');
      return $saleHistory;
    }

    return null;
  }

  public function delete(int $id){
    $saleHistory = $this->model->find($id);
    if($saleHistory != null){
      $saleHistory->is_active = 0;
      $saleHistory->save();
      $result = $saleHistory->delete();
      if($result){
        $saleHistory->deleted_st = Carbon::parse($saleHistory->deleted_at)->format('Y-m-d H:i:s');
        return $saleHistory;
      }
    }

    return false;
  }

  public function restore(int $id){
    $saleHistory = $this->model->withTrashed()->find($id);
    if($saleHistory != null && $saleHistory->trashed()){
      $saleHistory->is_active = 1;
      $saleHistory->save();
      $result = $saleHistory->restore();
      if($result){
        return $saleHistory;
      }
    }

    return false;
  }

}

?>