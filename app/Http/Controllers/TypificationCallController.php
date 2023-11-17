<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypificationCall;
use App\Services\Implementation\TypificationCallService;
use App\Validator\TypificationCallValidator;

class TypificationCallController extends Controller{

  private $request;
  private $typificationCallService;
  private $typificationCallValidator;


  public function __construct(Request $request, TypificationCallService $typificationCallService, TypificationCallValidator $typificationCallValidator)
  {
    $this->request = $request;
    $this->typificationCallService = $typificationCallService;
    $this->typificationCallValidator = $typificationCallValidator;
  }

  
  public function index(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->typificationCallService->index($data);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las tipificaciones', 'error' => $e->getMessage()], 500);
    }
  }

  public function listAll(){
    try{
      $result = $this->typificationCallService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las tipificaciones', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->typificationCallService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de la tipificación', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->typificationCallValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->typificationCallService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear la tipificación', 'error' => $e->getMessage()], 500);
    }
  }
  
  public function update($id){
    try{
      $validator = $this->typificationCallValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->typificationCallService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la tipificación', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos de la tipificación', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->typificationCallService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar la tipificación', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->typificationCallService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar la tipificación', 'error' => $e->getMessage()], 500);
    }
    
  }
}