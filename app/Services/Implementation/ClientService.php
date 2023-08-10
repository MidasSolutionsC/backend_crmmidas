<?php

namespace App\Services\Implementation;

use App\Models\Client;
use App\Services\Interfaces\IClient;
use Illuminate\Support\Carbon;

class ClientService implements IClient {
  private $model;

  public function __construct()
  {
    $this->model = new Client();
  }

  public function getAll(){
    $result = $this->model->get();
    foreach($result as $row){
      $row->fecha_creado = Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
      $row->fecha_modificado = Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
    }

    return $result;
  }

  public function getById(int $id){
    $client = $this->model->find($id);
    if($client){
      $client->fecha_creado = Carbon::parse($client->created_at)->format('d-m-Y H:i:s');
      $client->fecha_modificado = Carbon::parse($client->updated_at)->format('d-m-Y H:i:s');
    }

    return $client;
  }

  public function create(array $data){
    $client = $this->model->create($data);
    if($client){
      $client->fecha_creado = Carbon::parse($client->created_at)->format('d-m-Y H:i:s');
    }

    return $client;
  }

  public function update(array $data, int $id){
    $client = $this->model->find($id);
    if($client){
      $data['clave'] = password_hash($data['clave'], PASSWORD_BCRYPT);
      $client->fill($data);
      $client->save();
      $client->fecha_modificado = Carbon::parse($client->updated_at)->format('d-m-Y H:i:s');
      return $client;
    } else {
      return ['message' => 'Error al actualizar los datos del cliente'];
    }
  }

  public function delete(int $id){
    $client = $this->model->find($id);
    if(!is_null($client)){
      $client->estado = 0;
      $client->save();
      $result = $client->delete();
      if($result){
        $client->fecha_eliminado = Carbon::parse($client->deleted_at)->format('d-m-Y H:i:s');
        return $client;
      }
    } else {
      return ['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.'];
    }
  }

  public function restore(int $id){
    $client = $this->model->withTrashed()->find($id);
    if(!is_null($client) && $client->trashed()){
      $client->estado = 1;
      $client->save();
      $result = $client->restore();
      if($result){
        return $client;
      }
    } else {
      return ['message' => 'El recurso solicitado ha sido restaurado previamente.'];
    }
  }
}


?>