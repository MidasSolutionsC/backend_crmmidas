<?php 

namespace App\Services\Implementation;

use App\Models\Permission;
use App\Services\Interfaces\IPermission;
use Illuminate\Support\Carbon;

class PermissionService implements IPermission{
  
  private $model;

  public function __construct() {
    $this->model = new Permission();
  }

  public function getAll(){
    $result = $this->model->get();
    // Formatear la columna 'created_at' para cada registro
    foreach ($result as $row) {
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $permission = $this->model->find($id);
    if($permission){
      $permission->fecha_creado = Carbon::parse($permission->created_at)->format('d-m-Y H:i:s');
      $permission->fecha_modificado = Carbon::parse($permission->updated_at)->format('d-m-Y H:i:s');
    }
    return $permission;
  }

  public function create(array $data){
    $permission = $this->model->create($data);
    if($permission){
      $permission->fecha_creado = Carbon::parse($permission->created_at)->format('d-m-Y H:i:s');
    }

    return $permission;
  }

  public function update(array $data, int $id){
    $permission = $this->model->find($id);
    if($permission){
      $permission->fill($data);
      $permission->save();
      $permission->fecha_modificado = Carbon::parse($permission->updated_at)->format('d-m-Y H:i:s');
      return $permission;
    }

    return null;
  }

  public function delete(int $id){
    $permission = $this->model->find($id);
    if($permission != null){
      $permission->save();
      $result = $permission->delete();
      if($result){
        $permission->fecha_eliminado = Carbon::parse($permission->deleted_at)->format('d-m-Y H:i:s');
        return $permission;
      }
    }

    return false;
  }

  public function restore(int $id){
    $permission = $this->model->withTrashed()->find($id);
    if($permission != null && $permission->trashed()){
      $permission->save();
      $result = $permission->restore();
      if($result){
        return $permission;
      }
    }

    return false;
  }

}


?>