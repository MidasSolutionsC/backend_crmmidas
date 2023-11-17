<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\CampusUserService;
use App\Validator\CampusUserValidator;

class CampusUserController extends Controller{

  private $request;
  private $campusUserService;
  private $campusUserValidator;

  public function __construct(Request $request, CampusUserService $campusUserService, CampusUserValidator $campusUserValidator)
  {
    $this->request = $request;
    $this->campusUserService = $campusUserService;
    $this->campusUserValidator = $campusUserValidator;
  }

  public function listAll(){
    try{
      $result = $this->campusUserService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los usuarios de la sede', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByCampus(int $campusId){
    try{
      $result = $this->campusUserService->getFilterByCampus($campusId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los usuarios de la sede', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->campusUserService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de los usuarios', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->campusUserValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->campusUserService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear usuario de la sede', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->campusUserValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->campusUserService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del usuario de la sede', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del usuario de la sede', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->campusUserService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el usuario de la sede', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->campusUserService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el usuario de la sede', 'error' => $e->getMessage()], 500);
    }
    
  }
}