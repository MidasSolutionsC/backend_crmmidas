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
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $person = $this->model->find($id);
    if($person){
      $person->fecha_creado = Carbon::parse($person->created_at)->format('d-m-Y H:i:s');
      $person->fecha_modificado = Carbon::parse($person->updated_at)->format('d-m-Y H:i:s');
    }

    return $person;
  }

  public function create(array $data){
    $person = $this->model->create($data);
    if($person){
      $person->fecha_creado = Carbon::parse($person->created_at)->format('d-m-Y H:i:s');
    }

    return $person;
  }

  public function update(array $data, int $id){
    $person = $this->model->find($id);
    if($person){
      $person->fill($data);
      $person->save();
      $person->fecha_modificado = Carbon::parse($person->updated_at)->format('d-m-Y H:i:s');
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
        $person->fecha_eliminado = Carbon::parse($person->deleted_at)->format('d-m-Y H:i:s');
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