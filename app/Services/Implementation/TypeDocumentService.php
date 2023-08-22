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
    $existingRecord = $this->model->withTrashed()->where('nombre', $data['nombre'])->whereNotNull('deleted_at')->first();

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      $existingRecord->is_active = 1;
      $existingRecord->save();
      $tipoDocumento = $existingRecord->restore();
    } else {
      // No existe un registro con el mismo valor, puedes crear uno nuevo
      $tipoDocumento = $this->model->create($data);
      if($tipoDocumento){
        $tipoDocumento->created_at = Carbon::parse($tipoDocumento->created_at)->format('Y-m-d H:i:s');
      }
    }
    
    return $existingRecord;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $tipoDocumento = $this->model->find($id);
    if($tipoDocumento){
      $tipoDocumento->fill($data);
      $tipoDocumento->save();
      $tipoDocumento->updated_at = Carbon::parse($tipoDocumento->updated_at)->format('Y-m-d H:i:s');
      return $tipoDocumento;
    }

    return null;
  }

  public function delete(int $id){
    $tipoDocumento = $this->model->find($id);
    if($tipoDocumento != null){
      $tipoDocumento->is_active = 0;
      $tipoDocumento->save();
      $result = $tipoDocumento->delete();
      if($result){
        $tipoDocumento->deleted_st = Carbon::parse($tipoDocumento->deleted_at)->format('Y-m-d H:i:s');
        return $tipoDocumento;
      }
    }

    return false;
  }

  public function restore(int $id){
    $tipoDocumento = $this->model->withTrashed()->find($id);
    if($tipoDocumento != null && $tipoDocumento->trashed()){
      $tipoDocumento->is_active = 1;
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