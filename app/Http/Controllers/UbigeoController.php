<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\UbigeoService;
use App\Validator\UbigeoValidator;

class UbigeoController extends Controller{
  private $request;
  private $ubigeoService;
  private $ubigeoValidator;

  public function __construct(Request $request, UbigeoService $ubigeoService, UbigeoValidator $ubigeoValidator)
  {
    $this->request = $request;
    $this->ubigeoService = $ubigeoService;
    $this->ubigeoValidator = $ubigeoValidator;
  }
 
  public function listAll(){
    try{
      $result = $this->ubigeoService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los ubigeos', 'error' => $e->getMessage()], 500);
    }
  }
 
  public function search(){
    try{
      $result = $this->ubigeoService->search($this->request->all());
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los ubigeos', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->ubigeoService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del ubigeo', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->ubigeoValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->ubigeoService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el ubigeo', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->ubigeoValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->ubigeoService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del ubigeo', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del ubigeo', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->ubigeoService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el ubigeo', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->ubigeoService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el ubigeo', 'error' => $e->getMessage()], 500);
    }
    
  }
}