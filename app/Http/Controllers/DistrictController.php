<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\DistrictService;
use App\Validator\DistrictValidator;

class DistrictController extends Controller{

  private $request;
  private $districtService;
  private $districtValidator;

  public function __construct(Request $request, DistrictService $districtService, DistrictValidator $districtValidator)
  {
    $this->request = $request;
    $this->districtService = $districtService;
    $this->districtValidator = $districtValidator;
  }

  public function listAll(){
    try{
      $result = $this->districtService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los distritos', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByProvince(int $provinceId){
    try{
      $result = $this->districtService->getFilterByProvince($provinceId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los distritos', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->districtService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del distrito', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->districtValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->districtService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el distrito', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->districtValidator->validate('update');
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->districtService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del distrito', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del distrito', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->districtService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el distrito', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->districtService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el distrito', 'error' => $e->getMessage()], 500);
    }
    
  }
}