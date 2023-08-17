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
    $member = $this->model->create($data);
    if($member){
      $member->created_at = Carbon::parse($member->created_at)->format('Y-m-d H:i:s');
    }

    return $member;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
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