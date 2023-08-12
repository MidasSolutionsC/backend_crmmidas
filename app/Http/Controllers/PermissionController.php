<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\PermissionService;
use App\Validator\PermissionValidator;

class PermissionController extends Controller{

    
  private $request;
  private $permissionService;
  private $permissionValidator;

  public function __construct(Request $request, PermissionService $permissionService, PermissionValidator $permissionValidator) {
    $this->request = $request;
    $this->permissionService = $permissionService;
    $this->permissionValidator = $permissionValidator;
  }

  public function listAll(){
    try{
      $result = $this->permissionService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los tipos de documentos', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->permissionService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del tipo de documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->permissionValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->permissionService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el tipo de documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->permissionValidator->validate('update');
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->permissionService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del tipo de documento', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del tipo de documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->permissionService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el tipo de documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->permissionService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el tipo de documento', 'error' => $e->getMessage()], 500);
    }
    
  }
}