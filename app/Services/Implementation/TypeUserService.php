<?php

namespace App\Services\Implementation;

use App\Models\TypeUser;
use App\Services\Interfaces\ITypeUser;
use Illuminate\Support\Carbon;

class TypeUserService implements ITypeUser{

  private $model;

  public function __construct()
  {
    $this->model = new TypeUser();
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
  
  public function getByName(string $nombre){
    $query = $this->model->select();
    $result = $query->whereRaw('LOWER(nombre) = ?', [strtolower($nombre)])->first();
    return $result;
  }

  public function create(array $data){
    $existingRecord = $this->model->withTrashed()->where('nombre', $data['nombre'])->whereNotNull('deleted_at')->first();
    $typeUser = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      $existingRecord->updated_at = Carbon::now(); 
      $existingRecord->is_active = 1;
      $existingRecord->save();
      $result = $existingRecord->restore();
      if($result){
        $existingRecord->updated_at = Carbon::parse($existingRecord->updated_at)->format('Y-m-d H:i:s');
        $typeUser = $existingRecord;
      }
    } else {
      // No existe un registro con el mismo valor, puedes crear uno nuevo
      $data['created_at'] = Carbon::now(); 
      $typeUser = $this->model->create($data);
      if($typeUser){
        $typeUser->created_at = Carbon::parse($typeUser->created_at)->format('Y-m-d H:i:s');
      }
    }

    return $typeUser;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $typeUser = $this->model->find($id);
    if($typeUser){
      $typeUser->fill($data);
      $typeUser->save();
      $typeUser->updated_at = Carbon::parse($typeUser->updated_at)->format('Y-m-d H:i:s');
      return $typeUser;
    }

    return null;
  }

  public function delete(int $id){
    $typeUser = $this->model->find($id);
    if($typeUser != null){
      $typeUser->is_active = 0;
      $typeUser->save();
      $result = $typeUser->delete();
      if($result){
        $typeUser->deleted_st = Carbon::parse($typeUser->deleted_at)->format('Y-m-d H:i:s');
        return $typeUser;
      }
    }

    return false;
  }

  public function restore(int $id){
    $typeUser = $this->model->withTrashed()->find($id);
    if($typeUser != null && $typeUser->trashed()){
      $typeUser->is_active = 1;
      $typeUser->save();
      $result = $typeUser->restore();
      if($result){
        return $typeUser;
      }
    }

    return false;
  }
}

?>