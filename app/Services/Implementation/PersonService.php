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