<?php 

namespace App\Services\Implementation;

use App\Models\TypeDocument;
use App\Services\Interfaces\ITypeDocument;
use Illuminate\Support\Carbon;

class TypeDocumentService implements ITypeDocument{
  
  private $model;

  public function __construct() {
    $this->model = new TypeDocument();
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
    $tipoDocumento = $this->model->find($id);
    if($tipoDocumento){
      $tipoDocumento->fecha_creado = Carbon::parse($tipoDocumento->created_at)->format('d-m-Y H:i:s');
      $tipoDocumento->fecha_modificado = Carbon::parse($tipoDocumento->updated_at)->format('d-m-Y H:i:s');
    }
    return $tipoDocumento;
  }

  public function create(array $data){
    $tipoDocumento = $this->model->create($data);
    if($tipoDocumento){
      $tipoDocumento->fecha_creado = Carbon::parse($tipoDocumento->created_at)->format('d-m-Y H:i:s');
    }

    return $tipoDocumento;
  }

  public function update(array $data, int $id){
    $tipoDocumento = $this->model->find($id);
    if($tipoDocumento){
      $tipoDocumento->fill($data);
      $tipoDocumento->save();
      $tipoDocumento->fecha_modificado = Carbon::parse($tipoDocumento->updated_at)->format('d-m-Y H:i:s');
      return $tipoDocumento;
    }

    return null;
  }

  public function delete(int $id){
    $tipoDocumento = $this->model->find($id);
    if($tipoDocumento != null){
      $tipoDocumento->estado = 0;
      $tipoDocumento->save();
      $result = $tipoDocumento->delete();
      if($result){
        $tipoDocumento->fecha_eliminado = Carbon::parse($tipoDocumento->deleted_at)->format('d-m-Y H:i:s');
        return $tipoDocumento;
      }
    }

    return false;
  }

  public function restore(int $id){
    $tipoDocumento = $this->model->withTrashed()->find($id);
    if($tipoDocumento != null && $tipoDocumento->trashed()){
      $tipoDocumento->estado = 1;
      $tipoDocumento->save();
      $result = $tipoDocumento->restore();
      if($result){
        return $tipoDocumento;
      }
    }

    return false;
  }

}


?>