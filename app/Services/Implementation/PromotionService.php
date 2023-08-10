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
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $promotion = $this->model->find($id);
    if($promotion){
      $promotion->fecha_creado = Carbon::parse($promotion->created_at)->format('d-m-Y H:i:s');
      $promotion->fecha_modificado = Carbon::parse($promotion->updated_at)->format('d-m-Y H:i:s');
    }

    return $promotion;
  }

  public function create(array $data){
    $promotion = $this->model->create($data);
    if($promotion){
      $promotion->fecha_creado = Carbon::parse($promotion->created_at)->format('d-m-Y H:i:s');
    }

    return $promotion;
  }

  public function update(array $data, int $id){
    $promotion = $this->model->find($id);
    if($promotion){
      $promotion->fill($data);
      $promotion->save();
      $promotion->fecha_modificado = Carbon::parse($promotion->updated_at)->format('d-m-Y H:i:s');
      return $promotion;
    }

    return null;
  }

  public function delete(int $id){
    $promotion = $this->model->find($id);
    if($promotion != null){
      $promotion->estado = 0;
      $promotion->save();
      $result = $promotion->delete();
      if($result){
        $promotion->fecha_eliminado = Carbon::parse($promotion->deleted_at)->format('d-m-Y H:i:s');
        return $promotion;
      }
    }

    return false;
  }

  public function restore(int $id){
    $promotion = $this->model->withTrashed()->find($id);
    if($promotion != null && $promotion->trashed()){
      $promotion->estado = 1;
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