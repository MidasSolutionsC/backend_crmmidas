<?php

namespace App\Services\Implementation;

use App\Models\Client;
use App\Services\Interfaces\IClient;
use Illuminate\Support\Carbon;

class ClientService implements IClient {
  private $model;

  public function __construct()
  {
    $this->model = new Client();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getByPersonId(int $personId){
    $query = $this->model->query();
    if($personId){
      $query->where('personas_id', $personId);
    }
    $result = $query->get()->first();
    return $result;
  }

  public function getByCompanyId(int $companyId){
    $query = $this->model->query();
    if($companyId){
      $query->where('empresas_id', $companyId);
    }
    $result = $query->get()->first();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }

  public function create(array $data){
    $data['created_at'] = Carbon::now(); 
    $data['user_create_id'] = $data['user_auth_id'];
    $client = $this->model->create($data);
    if($client){
      $client->created_at = Carbon::parse($client->created_at)->format('Y-m-d H:i:s');
    }

    return $client;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id'];
    $client = $this->model->find($id);
    if($client){
      $client->fill($data);
      $client->save();
      $client->updated_at = Carbon::parse($client->updated_at)->format('Y-m-d H:i:s');
      return $client;
    } 
    
    return null;
  }

  public function delete(int $id){
    $client = $this->model->find($id);
    if(!is_null($client)){
      $client->is_active = 0;
      $client->save();
      $result = $client->delete();
      if($result){
        $client->deleted_st = Carbon::parse($client->deleted_at)->format('Y-m-d H:i:s');
        return $client;
      }
    } 

    return false;
  }

  public function restore(int $id){
    $client = $this->model->withTrashed()->find($id);
    if(!is_null($client) && $client->trashed()){
      $client->is_active = 1;
      $client->save();
      $result = $client->restore();
      if($result){
        return $client;
      }
    } 
    
    return false;
  }
}


?>