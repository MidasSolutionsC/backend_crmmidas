<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeService;
use App\Services\Implementation\TypeServiceService;
use App\Validator\TypeServiceValidator;

class TypeServiceController extends Controller{

  private $request;
  private $typeService;
  private $typeServiceValidator;

  public function __construct(Request $request, TypeServiceService $typeService, TypeServiceValidator $typeServiceValidator) {
    $this->request = $request;
    $this->typeService = $typeService;
    $this->typeServiceValidator = $typeServiceValidator;
  }

  public function listAll(){
    try{
      $result = $this->typeService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los tipos de servicios', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->typeService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del tipo de servicio', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->typeServiceValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        // $this->request->merge(['fecha_registro' => Carbon::now()]);
        $result = $this->typeService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el tipo de servicio', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->typeServiceValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->typeService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del tipo de servicio', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del tipo de servicio', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->typeService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el tipo de servicio', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->typeService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el tipo de servicio', 'error' => $e->getMessage()], 500);
    }
    
  }
}