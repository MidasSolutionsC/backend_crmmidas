<?php

namespace App\Services\Implementation;

use App\Models\TypeService;
use App\Services\Interfaces\ITypeService;
use Illuminate\Support\Carbon;

class TypeServiceService implements ITypeService{

  private $model;

  public function __construct()
  {
    $this->model = new TypeService();
  }

  public function getAll(){
    $result = $this->model->select('id', 'nombre', 'descripcion', 'estado')->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $typeService = $this->model->find($id);
    if($typeService){
      $typeService->fecha_creado = Carbon::parse($typeService->created_at)->format('d-m-Y H:i:s');
      $typeService->fecha_modificado = Carbon::parse($typeService->updated_at)->format('d-m-Y H:i:s');
    }

    return $typeService;
  }

  public function create(array $data){
    $typeService = $this->model->create($data);
    if($typeService){
      $typeService->fecha_creado = Carbon::parse($typeService->created_at)->format('d-m-Y H:i:s');
    }

    return $typeService;
  }

  public function update(array $data, int $id){
    $typeService = $this->model->find($id);
    if($typeService){
      $typeService->fill($data);
      $typeService->save();
      $typeService->fecha_modificado = Carbon::parse($typeService->updated_at)->format('d-m-Y H:i:s');
      return $typeService;
    }

    return null;
  }

  public function delete(int $id){
    $typeService = $this->model->find($id);
    if($typeService != null){
      $typeService->estado = 0;
      $typeService->save();
      $result = $typeService->delete();
      if($result){
        $typeService->fecha_eliminado = Carbon::parse($typeService->deleted_at)->format('d-m-Y H:i:s');
        return $typeService;
      }
    }

    return false;
  }

  public function restore(int $id){
    $typeService = $this->model->withTrashed()->find($id);
    if($typeService != null && $typeService->trashed()){
      $typeService->estado = 1;
      $typeService->save();
      $result = $typeService->restore();
      if($result){
        return $typeService;
      }
    }

    return false;
  }

}


?>