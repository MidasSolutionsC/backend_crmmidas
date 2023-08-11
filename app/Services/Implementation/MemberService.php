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
    $result = $this->model->select()->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $member = $this->model->find($id);
    if($member){
      $member->fecha_creado = Carbon::parse($member->created_at)->format('d-m-Y H:i:s');
      $member->fecha_modificado = Carbon::parse($member->updated_at)->format('d-m-Y H:i:s');
    }

    return $member;
  }

  public function create(array $data){
    $member = $this->model->create($data);
    if($member){
      $member->fecha_creado = Carbon::parse($member->created_at)->format('d-m-Y H:i:s');
    }

    return $member;
  }

  public function update(array $data, int $id){
    $member = $this->model->find($id);
    if($member){
      $member->fill($data);
      $member->save();
      $member->fecha_modificado = Carbon::parse($member->updated_at)->format('d-m-Y H:i:s');
      return $member;
    }

    return null;
  }

  public function delete(int $id){
    $member = $this->model->find($id);
    if($member != null){
      $member->estado = 0;
      $member->save();
      $result = $member->delete();
      if($result){
        $member->fecha_eliminado = Carbon::parse($member->deleted_at)->format('d-m-Y H:i:s');
        return $member;
      }
    }

    return false;
  }

  public function restore(int $id){
    $member = $this->model->withTrashed()->find($id);
    if($member != null && $member->trashed()){
      $member->estado = 1;
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