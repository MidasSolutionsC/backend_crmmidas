<?php

namespace App\Services\Implementation;

use App\Models\Address;
use App\Services\Interfaces\IAddress;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddressService implements IAddress{

  private $model;

  public function __construct()
  {
    $this->model = new Address();
  }

  public function getAll(){
    $query = $this->model->select();
    
    // $query->addSelect(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS fecha_creado"));
    // $query->addSelect(DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS fecha_modificado"));
    
    $result = $query->get();
    return $result;
  }

  public function getFilterByCompany(int $companyId){
    $query = $this->model->select();

    $query->selectRaw("CONCAT_WS(', ',
      CASE WHEN tipo IS NOT NULL AND tipo != '' THEN CONCAT(tipo, ' ', direccion) ELSE NULL END,
      CASE WHEN numero IS NOT NULL AND numero != '' THEN CONCAT(' N° ', numero) ELSE NULL END,
      CASE WHEN escalera IS NOT NULL AND escalera != '' THEN escalera ELSE NULL END,
      CASE WHEN portal IS NOT NULL AND portal != '' THEN portal ELSE NULL END,
      CASE WHEN planta IS NOT NULL AND planta != '' THEN planta ELSE NULL END,
      CASE WHEN puerta IS NOT NULL AND puerta != '' THEN puerta ELSE NULL END
    ) as direccion_completo");
    
    if($companyId){
      $query->where('empresas_id', $companyId);
    }
    
    $result = $query->get();
    return $result;
  }

  public function getFilterByPerson(int $personId){
    $query = $this->model->select();

    $query->selectRaw("CONCAT_WS(', ',
      CASE WHEN tipo IS NOT NULL AND tipo != '' THEN CONCAT(tipo, ' ', direccion) ELSE NULL END,
      CASE WHEN numero IS NOT NULL AND numero != '' THEN CONCAT(' N° ', numero) ELSE NULL END,
      CASE WHEN escalera IS NOT NULL AND escalera != '' THEN escalera ELSE NULL END,
      CASE WHEN portal IS NOT NULL AND portal != '' THEN portal ELSE NULL END,
      CASE WHEN planta IS NOT NULL AND planta != '' THEN planta ELSE NULL END,
      CASE WHEN puerta IS NOT NULL AND puerta != '' THEN puerta ELSE NULL END
    ) as direccion_completo");

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

    $address = $this->model->create($data);
    if($address){
      $address->created_at = Carbon::parse($address->created_at)->format('Y-m-d H:i:s');
    }

    return $address;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }

    $address = $this->model->find($id);
    if($address){
      $address->fill($data);
      $address->save();
      $address->updated_at = Carbon::parse($address->updated_at)->format('Y-m-d H:i:s');
      return $address;
    }

    return null;
  }

  public function delete(int $id){
    $address = $this->model->find($id);
    if($address != null){
      $address->is_active = 0;
      $address->save();
      $result = $address->delete();
      if($result){
        $address->deleted_st = Carbon::parse($address->deleted_at)->format('Y-m-d H:i:s');
        return $address;
      }
    }

    return false;
  }

  public function restore(int $id){
    $address = $this->model->withTrashed()->find($id);
    if($address != null && $address->trashed()){
      $address->is_active = 1;
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