<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\ClientService;
use App\Validator\ClientValidator;

class ClientController extends Controller{

  private $request;
  private $clientService;
  private $clientValidator;

  public function __construct(Request $request, ClientService $clientService, ClientValidator $clientValidator) {
    $this->request = $request;
    $this->clientService = $clientService;
    $this->clientValidator = $clientValidator;
  }

  public function listAll(){
    try{
      $result = $this->clientService->getAll();
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los clientes', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->clientService->getById($id);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->clientValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->clientService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->clientValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->clientService->update($this->request->all(), $id);
        if(!is_null($result)){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del cliente', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try {
      $result = $this->clientService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
      
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try {
      $result = $this->clientService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }

      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el cliente', 'error' => $e->getMessage()], 500);
    }
  }
}