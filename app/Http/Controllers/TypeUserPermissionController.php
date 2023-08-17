<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\TypeUserPermissionService;
use App\Validator\TypeUserPermissionValidator;

class TypeUserPermissionController extends Controller{

  private $request;
  private $typeUserPermissionService;
  private $typeUserPermissionValidator;

  public function __construct(Request $request, TypeUserPermissionService $typeUserPermissionService, TypeUserPermissionValidator $typeUserPermissionValidator)
  {
    $this->request = $request;
    $this->typeUserPermissionService = $typeUserPermissionService;
    $this->typeUserPermissionValidator = $typeUserPermissionValidator;
  }

  public function listAll(){
    try{
      $result = $this->typeUserPermissionService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los permisos de usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByTypeUser(int $typeUserId){
    try{
      $result = $this->typeUserPermissionService->getFilterByTypeUser($typeUserId);
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
      $result = $this->typeUserPermissionService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de los permisos de usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->typeUserPermissionValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->typeUserPermissionService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear permisos de usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->typeUserPermissionValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->typeUserPermissionService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del permiso de usuario', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del permiso de usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->typeUserPermissionService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el permiso de usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->typeUserPermissionService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el permiso de usuario', 'error' => $e->getMessage()], 500);
    }
    
  }
}