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
    $permission = $this->model->create($data);
    if($permission){
      $permission->created_at = Carbon::parse($permission->created_at)->format('Y-m-d H:i:s');
    }

    return $permission;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $permission = $this->model->find($id);
    if($permission){
      $permission->fill($data);
      $permission->save();
      $permission->updated_at = Carbon::parse($permission->updated_at)->format('Y-m-d H:i:s');
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
        $permission->deleted_st = Carbon::parse($permission->deleted_at)->format('Y-m-d H:i:s');
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