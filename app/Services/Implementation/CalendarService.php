<?php

namespace App\Services\Implementation;

use App\Models\Calendar;
use App\Services\Interfaces\ICalendar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class CalendarService implements ICalendar{

  private $model;

  public function __construct()
  {
    $this->model = new Calendar();
  }

  public function getAll(){
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterByUser(int $userId){
    $query = $this->model->select();
    if($userId){
      $query->where('user_create_id', $userId);
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
    $data['user_create_id'] = $data['user_auth_id']; 
    $calendar = $this->model->create($data);
    if($calendar){
      $calendar->created_at = Carbon::parse($calendar->created_at)->format('Y-m-d H:i:s');
    }

    return $calendar;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id']; 
    $calendar = $this->model->find($id);
    if($calendar){
      $calendar->fill($data);
      $calendar->save();
      $calendar->updated_at = Carbon::parse($calendar->updated_at)->format('Y-m-d H:i:s');
      return $calendar;
    }

    return null;
  }

  public function delete(int $id){
    $calendar = $this->model->find($id);
    if($calendar != null){
      $calendar->is_active = 0;
      $calendar->save();
      $result = $calendar->delete();
      if($result){
        $calendar->deleted_st = Carbon::parse($calendar->deleted_at)->format('Y-m-d H:i:s');
        return $calendar;
      }
    }

    return false;
  }

  public function restore(int $id){
    $calendar = $this->model->withTrashed()->find($id);
    if($calendar != null && $calendar->trashed()){
      $calendar->is_active = 1;
      $calendar->save();
      $result = $calendar->restore();
      if($result){
        return $calendar;
      }
    }

    return false;
  }

}


?>