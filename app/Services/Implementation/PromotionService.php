<?php

namespace App\Services\Implementation;

use App\Models\Promotion;
use App\Services\Interfaces\IPromotion;
use Illuminate\Support\Carbon;

class PromotionService implements IPromotion{

  private $model;

  public function __construct()
  {
    $this->model = new Promotion();
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
    $promotion = $this->model->create($data);
    if($promotion){
      $promotion->created_at = Carbon::parse($promotion->created_at)->format('Y-m-d H:i:s');
    }

    return $promotion;
  }

  public function update(array $data, int $id){
    $data['created_at'] = Carbon::now(); 
    $promotion = $this->model->find($id);
    if($promotion){
      $promotion->fill($data);
      $promotion->save();
      $promotion->updated_at = Carbon::parse($promotion->updated_at)->format('Y-m-d H:i:s');
      return $promotion;
    }

    return null;
  }

  public function delete(int $id){
    $promotion = $this->model->find($id);
    if($promotion != null){
      $promotion->is_active = 0;
      $promotion->save();
      $result = $promotion->delete();
      if($result){
        $promotion->deleted_st = Carbon::parse($promotion->deleted_at)->format('Y-m-d H:i:s');
        return $promotion;
      }
    }

    return false;
  }

  public function restore(int $id){
    $promotion = $this->model->withTrashed()->find($id);
    if($promotion != null && $promotion->trashed()){
      $promotion->is_active = 1;
      $promotion->save();
      $result = $promotion->restore();
      if($result){
        return $promotion;
      }
    }

    return false;
  }

}

?>