<?php

namespace App\Services\Implementation;

use App\Models\IdentificationDocument;
use App\Services\Interfaces\IIdentificationDocument;
use Illuminate\Support\Carbon;

class IdentificationDocumentService implements IIdentificationDocument{

  private $model;

  public function __construct()
  {
    $this->model = new IdentificationDocument();
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
    if(isset($data['user_auth_id'])){
      $data['user_create_id'] = $data['user_auth_id'];
    }

    $identyDocument = $this->model->create($data);
    if($identyDocument){
      $identyDocument->created_at = Carbon::parse($identyDocument->created_at)->format('Y-m-d H:i:s');
    }

    return $identyDocument;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }

    $identyDocument = $this->model->find($id);
    if($identyDocument){
      $identyDocument->fill($data);
      $identyDocument->save();
      $identyDocument->updated_at = Carbon::parse($identyDocument->updated_at)->format('Y-m-d H:i:s');
      return $identyDocument;
    }

    return null;
  }

  public function delete(int $id){
    $identyDocument = $this->model->find($id);
    if($identyDocument != null){
      $identyDocument->is_active = 0;
      $identyDocument->save();
      $result = $identyDocument->delete();
      if($result){
        $identyDocument->deleted_st = Carbon::parse($identyDocument->deleted_at)->format('Y-m-d H:i:s');
        return $identyDocument;
      }
    }

    return false;
  }

  public function restore(int $id){
    $identyDocument = $this->model->withTrashed()->find($id);
    if($identyDocument != null && $identyDocument->trashed()){
      $identyDocument->is_active = 1;
      $identyDocument->save();
      $result = $identyDocument->restore();
      if($result){
        return $identyDocument;
      }
    }

    return false;
  }

}


?>