<?php

namespace App\Services\Implementation;

use App\Models\Member;
use App\Services\Interfaces\IMember;
use Illuminate\Support\Carbon;

class MemberService implements IMember{

  private $model;

  public function __construct(){
    $this->model = new Member();
  }

  public function getAll(){
    $query = $this->model->query();
    $query->select(
      'integrantes.*',
      'PR.nombres as personas_nombres',
      'PR.apellido_paterno as personas_apellido_paterno',
      'PR.apellido_materno as personas_apellido_materno',
    );

    $query->join('grupos as GR', 'integrantes.grupos_id', 'GR.id');
    $query->join('usuarios as US', 'integrantes.usuarios_id', 'US.id');
    $query->join('personas as PR', 'US.personas_id', 'PR.id');

    $result = $query->get();
    return $result;
  }

  public function getByGroup(int $groupId){
    $query = $this->model->select();
    $query->select(
      'integrantes.*',
      'PR.nombres as nombres', 
      'PR.apellido_paterno as apellido_paterno', 
      'PR.apellido_materno as apellido_materno', 
      'US.nombre_usuario as nombre_usuario',
      'PR.documento as documento', 
      'PA.id as paises_id', 
      'PA.nombre as paises_nombre', 
      'TU.nombre as tipo_usuarios_nombre',
      'TD.id as tipo_documentos_id',
      'TD.abreviacion as tipo_documentos_abreviacion',
    );

    $query->join('grupos as GR', 'integrantes.grupos_id', 'GR.id');
    $query->join('usuarios as US', 'integrantes.usuarios_id', 'US.id');
    $query->join('personas as PR', 'US.personas_id', 'PR.id');
    $query->join('paises as PA', 'PR.paises_id', '=', 'PA.id');
    $query->join('tipo_documentos as TD', 'PR.tipo_documentos_id', '=', 'TD.id');
    $query->join('tipo_usuarios as TU', 'US.tipo_usuarios_id', '=', 'TU.id');

    if($groupId){
      $query->where('grupos_id', $groupId);
    }

    $result = $query->get();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->query();
    $query->select(
      'integrantes.*',
      'PR.nombres as personas_nombres',
      'PR.apellido_paterno as personas_apellido_paterno',
      'PR.apellido_materno as personas_apellido_materno',
    );

    $query->join('grupos as GR', 'integrantes.grupos_id', 'GR.id');
    $query->join('usuarios as US', 'integrantes.usuarios_id', 'US.id');
    $query->join('personas as PR', 'US.personas_id', 'PR.id');
    
    $result = $query->find($id);
    return $result;
  }

  public function create(array $data){
    $existingRecord = $this->model->withTrashed()
      ->where('grupos_id', $data['grupos_id'])
      ->where('usuarios_id', $data['usuarios_id'])
      ->whereNotNull('deleted_at')->first();
    $member = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      if(isset($data['user_auth_id'])){
        $existingRecord->user_update_id = $data['user_auth_id'];
      }
      $existingRecord->updated_at = Carbon::now(); 
      $existingRecord->is_active = 1;
      $existingRecord->save();
      $result = $existingRecord->restore();
      if($result){
        $existingRecord->updated_at = Carbon::parse($existingRecord->updated_at)->format('Y-m-d H:i:s');
        $member = $existingRecord;
      }
    } else {
      $data['created_at'] = Carbon::now(); 
      if(isset($data['user_auth_id'])){
        $data['user_create_id'] = $data['user_auth_id'];
      }
      $member = $this->model->create($data);
    }
    
    return $member;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }
    $member = $this->model->find($id);
    if($member){
      $member->fill($data);
      $member->save();
      $member->updated_at = Carbon::parse($member->updated_at)->format('Y-m-d H:i:s');
      return $member;
    }

    return null;
  }

  public function delete(int $id){
    $member = $this->model->find($id);
    if($member != null){
      $member->is_active = 0;
      $member->save();
      $result = $member->delete();
      if($result){
        $member->deleted_st = Carbon::parse($member->deleted_at)->format('Y-m-d H:i:s');
        return $member;
      }
    }

    return false;
  }

  public function restore(int $id){
    $member = $this->model->withTrashed()->find($id);
    if($member != null && $member->trashed()){
      $member->is_active = 1;
      $member->save();
      $result = $member->restore();
      if($result){
        return $member;
      }
    }

    return false;
  }

}


?>