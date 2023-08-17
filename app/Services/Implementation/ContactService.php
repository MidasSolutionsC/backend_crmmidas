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
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterByCompany(int $companyId){
    $query = $this->model->select();
    if($companyId){
      $query->where('empresas_id', $companyId);
    }

    $result = $query->get();
    return $result;
  }

  public function getFilterByPerson(int $personId){
    $query = $this->model->select();
    if($personId){
      $query->where('personas_id', $personId);
    }

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
    $contact = $this->model->create($data);
    if($contact){
      $contact->created_at = Carbon::parse($contact->created_at)->format('Y-m-d H:i:s');
    }

    return $contact;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $contact = $this->model->find($id);
    if($contact){
      $contact->fill($data);
      $contact->save();
      $contact->updated_at = Carbon::parse($contact->updated_at)->format('Y-m-d H:i:s');
      return $contact;
    }

    return null;
  }

  public function delete(int $id){
    $contact = $this->model->find($id);
    if($contact != null){
      $contact->is_active = 0;
      $contact->save();
      $result = $contact->delete();
      if($result){
        $contact->deleted_st = Carbon::parse($contact->deleted_at)->format('Y-m-d H:i:s');
        return $contact;
      }
    }

    return false;
  }

  public function restore(int $id){
    $contact = $this->model->withTrashed()->find($id);
    if($contact != null && $contact->trashed()){
      $contact->is_active = 1;
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