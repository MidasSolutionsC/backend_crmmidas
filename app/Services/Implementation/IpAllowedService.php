<?php

namespace App\Services\Implementation;

use App\Models\IpAllowed;
use App\Services\Interfaces\IIpAllowed;
use Illuminate\Support\Carbon;

class IpAllowedService implements IIpAllowed
{

  private $model;

  public function __construct()
  {
    $this->model = new IpAllowed();
  }

  public function getAll()
  {
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getById(int $id)
  {
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }


  public function getFilterByIP(string $ip)
  {
    $query = $this->model->select();
    if ($ip) {
      $query->where('ip', $ip);
    }

    $result = $query->get();
    return $result;
  }


  public function create(array $data)
  {
    $existingRecord = $this->model->withTrashed()
      ->where('ip', $data['ip'])
      ->whereNotNull('deleted_at')->first();

    $ipAllowed = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      if(isset($data["user_auth_id"])){
        $data["user_update_id"] = $data["user_auth_id"];
      }
      $existingRecord->updated_at = Carbon::now();
      $existingRecord->save();
      $result = $existingRecord->restore();
      if ($result) {
        $ipAllowed = $existingRecord;
      }
    } else {
      // No existe un registro con el mismo valor, puedes crear uno nuevo
      if(isset($data["user_auth_id"])){
        $data["user_create_id"] = $data["user_auth_id"];
      }
      $data['created_at'] = Carbon::now();
      $ipAllowed = $this->model->create($data);
    }

    return $ipAllowed;
  }

  public function update(array $data, int $id)
  {
    if(isset($data["user_auth_id"])){
      $data["user_update_id"] = $data["user_auth_id"];
    }
    $data['updated_at'] = Carbon::now();
    $ipAllowed = $this->model->find($id);
    if ($ipAllowed) {
      $ipAllowed->fill($data);
      $ipAllowed->save();
      return $ipAllowed;
    }

    return null;
  }

  public function delete(int $id)
  {
    $ipAllowed = $this->model->find($id);
    if ($ipAllowed != null) {
      $ipAllowed->save();
      $result = $ipAllowed->delete();
      if ($result) {
        $ipAllowed->deleted_st = Carbon::parse($ipAllowed->deleted_at)->format('Y-m-d H:i:s');
        return $ipAllowed;
      }
    }

    return false;
  }

  public function restore(int $id)
  {
    $ipAllowed = $this->model->withTrashed()->find($id);
    if ($ipAllowed != null && $ipAllowed->trashed()) {
      $ipAllowed->save();
      $result = $ipAllowed->restore();
      if ($result) {
        return $ipAllowed;
      }
    }

    return false;
  }
}
