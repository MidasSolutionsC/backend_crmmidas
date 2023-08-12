<?php

namespace App\Services\Implementation;

use App\Models\Address;
use App\Services\Interfaces\IAddress;
use Illuminate\Support\Carbon;

class AddressService implements IAddress{

  private $model;

  public function __construct()
  {
    $this->model = new Address();
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
    $address = $this->model->find($id);
    if($address){
      $address->fecha_creado = Carbon::parse($address->created_at)->format('d-m-Y H:i:s');
      $address->fecha_modificado = Carbon::parse($address->updated_at)->format('d-m-Y H:i:s');
    }

    return $address;
  }

  public function create(array $data){
    $address = $this->model->create($data);
    if($address){
      $address->fecha_creado = Carbon::parse($address->created_at)->format('d-m-Y H:i:s');
    }

    return $address;
  }

  public function update(array $data, int $id){
    $address = $this->model->find($id);
    if($address){
      $address->fill($data);
      $address->save();
      $address->fecha_modificado = Carbon::parse($address->updated_at)->format('d-m-Y H:i:s');
      return $address;
    }

    return null;
  }

  public function delete(int $id){
    $address = $this->model->find($id);
    if($address != null){
      $address->estado = 0;
      $address->save();
      $result = $address->delete();
      if($result){
        $address->fecha_eliminado = Carbon::parse($address->deleted_at)->format('d-m-Y H:i:s');
        return $address;
      }
    }

    return false;
  }

  public function restore(int $id){
    $address = $this->model->withTrashed()->find($id);
    if($address != null && $address->trashed()){
      $address->estado = 1;
      $address->save();
      $result = $address->restore();
      if($result){
        return $address;
      }
    }

    return false;
  }

}


?>