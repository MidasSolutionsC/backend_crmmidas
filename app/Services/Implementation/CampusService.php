<?php

namespace App\Services\Implementation;

use App\Models\Campus;
use App\Services\Interfaces\ICampus;
use Illuminate\Support\Carbon;

class CampusService implements ICampus{

  private $model;

  public function __construct()
  {
    $this->model = new Campus();
  }

  public function getAll(){
    $query = $this->model->select(
      'sedes.*',
      'PS.nombre as paises_nombre',
    );

    $query->selectRaw("CONCAT(UB.dpto, ', ', UB.prov, ', ', UB.distrito) as ubigeos_ciudad");
    $query->join('paises as PS', 'sedes.paises_id', '=', 'PS.id');
    $query->leftJoin('ubigeos as UB', 'sedes.codigo_ubigeo', '=', 'UB.ubigeo');
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
    $campus = $this->model->create($data);
    if($campus){
      $country = $campus->country;
      $ubigeo = $campus->ubigeo;

      $campus->paises_nombre = $country->nombre;
      $campus->ubigeos_ciudad = $ubigeo->dpto . " " . $ubigeo->prov . " " . $ubigeo->distrito;
    }

    return $campus;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $campus = $this->model->find($id);
    if($campus){
      $campus->fill($data);
      $campus->save();
      $country = $campus->country;
      $ubigeo = $campus->ubigeo;
      
      $campus->paises_nombre = $country->nombre;
      $campus->ubigeos_ciudad = $ubigeo->dpto . ", " . $ubigeo->prov . ", " . $ubigeo->distrito;
      return $campus;
    }

    return null;
  }

  public function delete(int $id){
    $campus = $this->model->find($id);
    if($campus != null){
      $campus->is_active = false;
      $campus->save();
      $result = $campus->delete();
      if($result){
        $campus->deleted_st = Carbon::parse($campus->deleted_at)->format('Y-m-d H:i:s');
        return $campus;
      }
    }

    return false;
  }

  public function restore(int $id){
    $campus = $this->model->withTrashed()->find($id);
    if($campus != null && $campus->trashed()){
      $campus->is_active = true;
      $campus->save();
      $result = $campus->restore();
      if($result){
        return $campus;
      }
    }

    return false;
  }

}


?>