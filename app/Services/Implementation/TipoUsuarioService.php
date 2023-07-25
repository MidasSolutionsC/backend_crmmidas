<?php

namespace App\Services\Implementation;

use App\Models\TipoUsuario;
use App\Services\Interfaces\ITipoUsuario;
use Illuminate\Support\Carbon;

class TipoUsuarioService implements ITipoUsuario{

  private $model;

  public function __construct()
  {
    $this->model = new TipoUsuario();
  }

  public function getAll(){
    $result = $this->model->select('id', 'nombre', 'descripcion', 'estado')->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
    //return $this->model->get();
    //return $this->model->withTrashed()->get(); // Incluido los eliminados
  }

  public function getById(int $id){
    $tipoUsuario = $this->model->find($id);
    if($tipoUsuario){
      $tipoUsuario->fecha_creado = Carbon::parse($tipoUsuario->created_at)->format('d-m-Y H:i:s');
      $tipoUsuario->fecha_modificado = Carbon::parse($tipoUsuario->updated_at)->format('d-m-Y H:i:s');
    }

    return $tipoUsuario;
    //return $this->model->where('id', $id)->first();
  }

  public function create(array $data){
    $tipoUsuario = $this->model->create($data);
    if($tipoUsuario){
      $tipoUsuario->fecha_creado = Carbon::parse($tipoUsuario->created_at)->format('d-m-Y H:i:s');
    }

    return $tipoUsuario;
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
      $tipoUsuario->fecha_modificado = Carbon::parse($tipoUsuario->updated_at)->format('d-m-Y H:i:s');
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
        $tipoUsuario->fecha_eliminado = Carbon::parse($tipoUsuario->deleted_at)->format('d-m-Y H:i:s');
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