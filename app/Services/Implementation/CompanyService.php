<?php

namespace App\Services\Implementation;

use App\Models\Company;
use App\Services\Interfaces\ICompany;
use Illuminate\Support\Carbon;

class CompanyService implements ICompany{

  private $model;

  public function __construct()
  {
    $this->model = new Company();
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
    if(isset($data['user_auth_id'])){
      $data['user_create_id'] = $data['user_auth_id'];
    }
    $company = $this->model->create($data);
    if($company){
      $company->created_at = Carbon::parse($company->created_at)->format('Y-m-d H:i:s');
    }

    return $company;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }
    $company = $this->model->find($id);
    if($company){
      $company->fill($data);
      $company->save();
      $company->updated_at = Carbon::parse($company->updated_at)->format('Y-m-d H:i:s');
      return $company;
    }

    return null;
  }

  public function delete(int $id){
    $company = $this->model->find($id);
    if($company != null){
      $company->is_active = 0;
      $company->save();
      $result = $company->delete();
      if($result){
        $company->deleted_st = Carbon::parse($company->deleted_at)->format('Y-m-d H:i:s');
        return $company;
      }
    }

    return false;
  }

  public function restore(int $id){
    $company = $this->model->withTrashed()->find($id);
    if($company != null && $company->trashed()){
      $company->is_active = 1;
      $company->save();
      $result = $company->restore();
      if($result){
        return $company;
      }
    }

    return false;
  }

}


?>