<?php 

namespace App\Services\Implementation;

use App\Models\Manual;
use App\Services\Interfaces\IManual;
use Illuminate\Support\Carbon;

class ManualService implements IManual{
  
  private $model;

  public function __construct() {
    $this->model = new Manual();
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
    $manual = $this->model->create($data);
    if($manual){
      $manual->created_at = Carbon::parse($manual->created_at)->format('Y-m-d H:i:s');
    }

    return $manual;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $manual = $this->model->find($id);
    if($manual){
      $manual->fill($data);
      $manual->save();
      $manual->updated_at = Carbon::parse($manual->updated_at)->format('Y-m-d H:i:s');
      return $manual;
    }

    return null;
  }

  public function delete(int $id){
    $manual = $this->model->find($id);
    if($manual != null){
      $manual->save();
      $result = $manual->delete();
      if($result){
        $manual->deleted_st = Carbon::parse($manual->deleted_at)->format('Y-m-d H:i:s');
        return $manual;
      }
    }

    return false;
  }

  public function restore(int $id){
    $manual = $this->model->withTrashed()->find($id);
    if($manual != null && $manual->trashed()){
      $manual->save();
      $result = $manual->restore();
      if($result){
        return $manual;
      }
    }

    return false;
  }

}


?>