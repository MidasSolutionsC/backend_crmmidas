<?php

namespace App\Services\Implementation;

use App\Models\TipoUsuario;
use App\Services\Interfaces\ITipoUsuario;

class TipoUsuarioService implements ITipoUsuario{

  private $model;

  public function __construct()
  {
    $this->model = new TipoUsuario();
  }

  public function getAll(){
    return $this->model->select('id', 'nombre', 'descripcion', 'estado')->get();
    //return $this->model->get();
    //return $this->model->withTrashed()->get(); // Incluido los eliminados
  }

  public function getById(int $id){
    return $this->model->where('id', $id)->first();
  }

  public function create(array $data){
    return $this->model->create($data);
  }

  public function update(array $data, int $id){
    // return $this->model->where('id', $id)
    //   ->first()
    //   ->fill($data)
    //   ->save();
    $tipoUsuario = $this->model->find($id);
    if($tipoUsuario){
      $tipoUsuario->fill($data);
      $tipoUsuario->save();
      return $tipoUsuario;
    }

    return null;
  }

  public function delete(int $id){
    $tipoUsuario = $this->model->find($id);
    if($tipoUsuario != null){
      $tipoUsuario->estado = 0;
      $tipoUsuario->save();
      $result = $tipoUsuario->delete();
      if($result){
        return $tipoUsuario;
      }
    }

    return false;
  }

  public function restore(int $id){
    $tipoUsuario = $this->model->withTrashed()->find($id);
    if($tipoUsuario != null && $tipoUsuario->trashed()){
      $tipoUsuario->estado = 1;
      $tipoUsuario->save();
      $result = $tipoUsuario->restore();
      if($result){
        return $tipoUsuario;
      }
    }

    return false;
  }
}

?>