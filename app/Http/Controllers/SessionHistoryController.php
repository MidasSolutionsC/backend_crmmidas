<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\SessionHistoryService;
use App\Validator\SessionHistoryValidator;

class SessionHistoryController extends Controller{

  private $request;
  private $sessionHistoryService;
  private $sessionHistoryValidator;

  public function __construct(Request $request, SessionHistoryService $sessionHistoryService, SessionHistoryValidator $sessionHistoryValidator)
  {
    $this->request = $request;
    $this->sessionHistoryService = $sessionHistoryService;
    $this->sessionHistoryValidator = $sessionHistoryValidator;
  }

  public function listAll(){
    try{
      $result = $this->sessionHistoryService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los historiales de sesiones', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByUser($userId){
    try{
      $result = $this->sessionHistoryService->getFilterByUser($userId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los historiales de sesiones', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->sessionHistoryService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del historial de sesión', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->sessionHistoryValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $requestData = $this->request->all();
        $requestData['ip_address'] = $this->request->ip(); // Obtener la dirección IP

        $result = $this->sessionHistoryService->create($requestData);
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el historial de sesión', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->sessionHistoryValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $requestData = $this->request->all();
        $requestData['ip_address'] = $this->request->ip();

        $result = $this->sessionHistoryService->update($requestData, $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del historial de sesión', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del historial de sesión', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->sessionHistoryService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el historial de sesión', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->sessionHistoryService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el historial de sesión', 'error' => $e->getMessage()], 500);
    }
    
  }
}