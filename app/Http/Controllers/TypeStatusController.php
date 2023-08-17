<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\TypeStatusService;
use App\Validator\TypeStatusValidator;

class TypeStatusController extends Controller{

  private $request;
  private $typeStatusService;
  private $typeStatusValidator;

  public function __construct(Request $request, TypeStatusService $typeStatusService, TypeStatusValidator $typeStatusValidator) {
    $this->request = $request;
    $this->typeStatusService = $typeStatusService;
    $this->typeStatusValidator = $typeStatusValidator;
  }

  public function listAll(){
    try{
      $result = $this->typeStatusService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los tipos de estados', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->typeStatusService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del tipo de estado', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->typeStatusValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->typeStatusService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el tipo de estado', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->typeStatusValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->typeStatusService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del tipo de estado', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del tipo de estado', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->typeStatusService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el tipo de estado', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->typeStatusService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el tipo de estado', 'error' => $e->getMessage()], 500);
    }
    
  }
}