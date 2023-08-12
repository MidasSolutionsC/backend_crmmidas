<?php

namespace App\Services\Implementation;

use App\Models\Contact;
use App\Services\Interfaces\IContact;
use Illuminate\Support\Carbon;

class ContactService implements IContact{

  private $model;

  public function __construct()
  {
    $this->model = new Contact();
  }

  public function getAll(){
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getFilterByCompany(int $companyId){
    $query = $this->model->select();
    if($companyId){
      $query->where('empresas_id', $companyId);
    }
    
    $result = $query->get();

    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getFilterByPerson(int $personId){
    $query = $this->model->select();
    if($personId){
      $query->where('personas_id', $personId);
    }
    
    $result = $query->get();

    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $contact = $this->model->find($id);
    if($contact){
      $contact->fecha_creado = Carbon::parse($contact->created_at)->format('d-m-Y H:i:s');
      $contact->fecha_modificado = Carbon::parse($contact->updated_at)->format('d-m-Y H:i:s');
    }

    return $contact;
  }

  public function create(array $data){
    $contact = $this->model->create($data);
    if($contact){
      $contact->fecha_creado = Carbon::parse($contact->created_at)->format('d-m-Y H:i:s');
    }

    return $contact;
  }

  public function update(array $data, int $id){
    $contact = $this->model->find($id);
    if($contact){
      $contact->fill($data);
      $contact->save();
      $contact->fecha_modificado = Carbon::parse($contact->updated_at)->format('d-m-Y H:i:s');
      return $contact;
    }

    return null;
  }

  public function delete(int $id){
    $contact = $this->model->find($id);
    if($contact != null){
      $contact->estado = 0;
      $contact->save();
      $result = $contact->delete();
      if($result){
        $contact->fecha_eliminado = Carbon::parse($contact->deleted_at)->format('d-m-Y H:i:s');
        return $contact;
      }
    }

    return false;
  }

  public function restore(int $id){
    $contact = $this->model->withTrashed()->find($id);
    if($contact != null && $contact->trashed()){
      $contact->estado = 1;
      $contact->save();
      $result = $contact->restore();
      if($result){
        return $contact;
      }
    }

    return false;
  }

}


?>