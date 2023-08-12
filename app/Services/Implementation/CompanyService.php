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
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $company = $this->model->find($id);
    if($company){
      $company->fecha_creado = Carbon::parse($company->created_at)->format('d-m-Y H:i:s');
      $company->fecha_modificado = Carbon::parse($company->updated_at)->format('d-m-Y H:i:s');
    }

    return $company;
  }

  public function create(array $data){
    $company = $this->model->create($data);
    if($company){
      $company->fecha_creado = Carbon::parse($company->created_at)->format('d-m-Y H:i:s');
    }

    return $company;
  }

  public function update(array $data, int $id){
    $company = $this->model->find($id);
    if($company){
      $company->fill($data);
      $company->save();
      $company->fecha_modificado = Carbon::parse($company->updated_at)->format('d-m-Y H:i:s');
      return $company;
    }

    return null;
  }

  public function delete(int $id){
    $company = $this->model->find($id);
    if($company != null){
      $company->estado = 0;
      $company->save();
      $result = $company->delete();
      if($result){
        $company->fecha_eliminado = Carbon::parse($company->deleted_at)->format('d-m-Y H:i:s');
        return $company;
      }
    }

    return false;
  }

  public function restore(int $id){
    $company = $this->model->withTrashed()->find($id);
    if($company != null && $company->trashed()){
      $company->estado = 1;
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