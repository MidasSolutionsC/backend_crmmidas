<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\ServiceHistoryService;
use App\Validator\ServiceHistoryValidator;

class ServiceHistoryController extends Controller{

  private $request;
  private $serviceHistoryService;
  private $serviceHistoryValidator;

  public function __construct(Request $request, ServiceHistoryService $serviceHistoryService, ServiceHistoryValidator $serviceHistoryValidator)
  {
    $this->request = $request;
    $this->serviceHistoryService = $serviceHistoryService;
    $this->serviceHistoryValidator = $serviceHistoryValidator;
  }

  public function listAll(){
    try{
      $result = $this->serviceHistoryService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los historiales', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByService($serviceId){
    try{
      $result = $this->serviceHistoryService->getFilterByService($serviceId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los historiales', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->serviceHistoryService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del historial', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->serviceHistoryValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->serviceHistoryService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el historial', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->serviceHistoryValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->serviceHistoryService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del historial', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del historial', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->serviceHistoryService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el historial', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->serviceHistoryService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el historial', 'error' => $e->getMessage()], 500);
    }
    
  }
}