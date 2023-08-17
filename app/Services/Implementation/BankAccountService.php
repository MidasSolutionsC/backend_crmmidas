<?php

namespace App\Services\Implementation;

use App\Models\BankAccount;
use App\Services\Interfaces\IBankAccount;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class BankAccountService implements IBankAccount{

  private $model;

  public function __construct()
  {
    $this->model = new BankAccount();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterByClient(int $clientId){
    $query = $this->model->select();
    if($clientId){
      $query->where('clientes_id', $clientId);
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
    $bankAccount = $this->model->create($data);
    if($bankAccount){
      $bankAccount->created_at = Carbon::parse($bankAccount->created_at)->format('Y-m-d H:i:s');
    }

    return $bankAccount;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $bankAccount = $this->model->find($id);
    if($bankAccount){
      $bankAccount->fill($data);
      $bankAccount->save();
      $bankAccount->updated_at = Carbon::parse($bankAccount->updated_at)->format('Y-m-d H:i:s');
      return $bankAccount;
    }

    return null;
  }

  public function delete(int $id){
    $bankAccount = $this->model->find($id);
    if($bankAccount != null){
      $bankAccount->is_active = 0;
      $bankAccount->save();
      $result = $bankAccount->delete();
      if($result){
        $bankAccount->deleted_st = Carbon::parse($bankAccount->deleted_at)->format('Y-m-d H:i:s');
        return $bankAccount;
      }
    }

    return false;
  }

  public function restore(int $id){
    $bankAccount = $this->model->withTrashed()->find($id);
    if($bankAccount != null && $bankAccount->trashed()){
      $bankAccount->is_active = 1;
      $bankAccount->save();
      $result = $bankAccount->restore();
      if($result){
        return $bankAccount;
      }
    }

    return false;
  }

}


?>