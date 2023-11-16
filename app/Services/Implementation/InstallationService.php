<?php

namespace App\Services\Implementation;

use App\Models\Installation;
use App\Services\Interfaces\IInstallation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InstallationService implements IInstallation{

  private $model;

  public function __construct()
  {
    $this->model = new Installation();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  
  public function search(array $data){
    $search = $data['search'];
    $ventasId = !empty($data['ventas_id'])? $data['ventas_id']: null;
    $limit = !empty($data['limit'])? $data['limit']: 25;

    $query = $this->model->select();
    $query->selectRaw("CONCAT_WS(', ',
      CASE WHEN tipo IS NOT NULL AND tipo != '' THEN CONCAT(tipo, ' ', direccion) ELSE NULL END,
      CASE WHEN numero IS NOT NULL AND numero != '' THEN CONCAT(' N° ', numero) ELSE NULL END,
      CASE WHEN escalera IS NOT NULL AND escalera != '' THEN escalera ELSE NULL END,
      CASE WHEN portal IS NOT NULL AND portal != '' THEN portal ELSE NULL END,
      CASE WHEN planta IS NOT NULL AND planta != '' THEN planta ELSE NULL END,
      CASE WHEN puerta IS NOT NULL AND puerta != '' THEN puerta ELSE NULL END
    ) as direccion_completo");

    if(!is_null($ventasId)){
      $query->where('ventas_id', $ventasId);
    }

    $query->havingRaw("direccion_completo like ?", ['%' . $search . '%']);
    $query->take($limit); // Limite de resultados
    $result = $query->get();
    return $result;
  }

  public function getBySale(int $saleId){
    $fields = [
      "id", 
      "ventas_id", 
      "codigo_postal",
      "localidad",
      "provincia",
      DB::raw("CONCAT(tipo, ' ',  direccion, ', N° ', numero, ', ', escalera, ', ',  portal, ', ',  planta, ' ',  puerta) as direccion_completo")
    ];

    $query = $this->model->select();

    $query->selectRaw("CONCAT_WS(', ',
      CASE WHEN tipo IS NOT NULL AND tipo != '' THEN CONCAT(tipo, ' ', direccion) ELSE NULL END,
      CASE WHEN numero IS NOT NULL AND numero != '' THEN CONCAT(' N° ', numero) ELSE NULL END,
      CASE WHEN escalera IS NOT NULL AND escalera != '' THEN escalera ELSE NULL END,
      CASE WHEN portal IS NOT NULL AND portal != '' THEN portal ELSE NULL END,
      CASE WHEN planta IS NOT NULL AND planta != '' THEN planta ELSE NULL END,
      CASE WHEN puerta IS NOT NULL AND puerta != '' THEN puerta ELSE NULL END
    ) as direccion_completo");

    if($saleId){
      $query->where('ventas_id', $saleId);
    }

    $result = $query->get();
    return $result;
  }

  public function getByAddress(int $addressId){
    $query = $this->model->query();

    $query->select();
    $query->selectRaw("CONCAT_WS(', ',
      CASE WHEN tipo IS NOT NULL AND tipo != '' THEN CONCAT(tipo, ' ', direccion) ELSE NULL END,
      CASE WHEN numero IS NOT NULL AND numero != '' THEN CONCAT(' N° ', numero) ELSE NULL END,
      CASE WHEN escalera IS NOT NULL AND escalera != '' THEN escalera ELSE NULL END,
      CASE WHEN portal IS NOT NULL AND portal != '' THEN portal ELSE NULL END,
      CASE WHEN planta IS NOT NULL AND planta != '' THEN planta ELSE NULL END,
      CASE WHEN puerta IS NOT NULL AND puerta != '' THEN puerta ELSE NULL END
    ) as direccion_completo");

    if($addressId){
      $query->where('direcciones_id', $addressId);
    }

    $query->latest();

    $result = $query->first();
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
    
    $installation = $this->model->create($data);
    if($installation){
      $installation->created_at = Carbon::parse($installation->created_at)->format('Y-m-d H:i:s');
    }

    return $installation;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }

    $installation = $this->model->find($id);

    if($installation){
      $installation->fill($data);
      $installation->save();
      $installation->updated_at = Carbon::parse($installation->updated_at)->format('Y-m-d H:i:s');
      return $installation;
    }

    return null;
  }

  public function delete(int $id){
    $installation = $this->model->find($id);
    if($installation != null){
      $installation->is_active = 0;
      $installation->save();
      $result = $installation->delete();
      if($result){
        $installation->deleted_st = Carbon::parse($installation->deleted_at)->format('Y-m-d H:i:s');
        return $installation;
      }
    }

    return false;
  }

  public function restore(int $id){
    $installation = $this->model->withTrashed()->find($id);
    if($installation != null && $installation->trashed()){
      $installation->is_active = 1;
      $installation->save();
      $result = $installation->restore();
      if($result){
        return $installation;
      }
    }

    return false;
  }

}

?>