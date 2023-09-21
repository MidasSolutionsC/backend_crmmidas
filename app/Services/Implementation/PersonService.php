<?php

namespace App\Services\Implementation;

use App\Models\Person;
use App\Services\Interfaces\IPerson;
use Illuminate\Support\Carbon;

class PersonService implements IPerson{

  private $model;

  public function __construct()
  {
    $this->model = new Person();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function search(array $data){
    $search = $data['search'];
    $typeDocumentId = !empty($data['tipo_documentos_id'])? $data['tipo_documentos_id']: null;
    $document = !empty($data['documento'])? $data['documento']: null;

    $query = $this->model->query();

    $query->select(
      'personas.*',
      'TD.nombre as tipo_documentos_nombre',
      'TD.abreviacion as tipo_documentos_abreviacion',
      'PS.nombre as paises_nombre',

    );

    $query->join('paises as PS', 'personas.paises_id', 'PS.id');
    $query->join('tipo_documentos as TD', 'personas.tipo_documentos_id', 'TD.id');

    if(!is_null($typeDocumentId)){
      $query->where('tipo_documentos_id', $typeDocumentId);
    }

    if(!is_null($document)){
      $query->where('documento', 'like', ['%' . $document . '%']);
    }

    $query->where(function ($query) use ($search) {
      $query->where('nombres', 'like', '%' . $search . '%')
          ->orWhere('apellido_paterno', 'like', '%' . $search . '%')
          ->orWhere('apellido_materno', 'like', '%' . $search . '%')
          ->orWhere('documento', 'like', '%' . $search . '%');
    });
  
    $query->take(25); // Limite de resultados
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
    $person = $this->model->create($data);
    if($person){
      $person->created_at = Carbon::parse($person->created_at)->format('Y-m-d H:i:s');
      $person->paises_nombre = $person->country->nombre;
      $person->tipo_documentos_abreviacion = $person->typeDocument->abreviacion;
    }

    return $person;
  }

  public function update(array $data, int $id){
    $data['created_at'] = Carbon::now(); 
    $person = $this->model->find($id);
    if($person){
      $person->fill($data);
      $person->save();
      $person->updated_at = Carbon::parse($person->updated_at)->format('Y-m-d H:i:s');
      $person->paises_nombre = $person->country->nombre;
      $person->tipo_documentos_abreviacion = $person->typeDocument->abreviacion;
      return $person;
    }

    return null;
  }

  public function delete(int $id){
    $person = $this->model->find($id);
    if($person != null){
      $person->save();
      $result = $person->delete();
      if($result){
        $person->deleted_st = Carbon::parse($person->deleted_at)->format('Y-m-d H:i:s');
        return $person;
      }
    }

    return false;
  }

  public function restore(int $id){
    $person = $this->model->withTrashed()->find($id);
    if($person != null && $person->trashed()){
      $person->save();
      $result = $person->restore();
      if($result){
        return $person;
      }
    }

    return false;
  }

}


?>