<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Services\Implementation\BrandService;
use App\Validator\BrandValidator;

class BrandController extends Controller{
  private $request;
  private $brandService;
  private $brandValidator;

  public function __construct(Request $request, BrandService $brandService, BrandValidator $brandValidator)
  {
    $this->request = $request;
    $this->brandService = $brandService;
    $this->brandValidator = $brandValidator;
  }

  public function index(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->brandService->index($data);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las marcas', 'error' => $e->getMessage()], 500);
    }
  }

  public function listAll(){
    try{
      $result = $this->brandService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las marcas', 'error' => $e->getMessage()], 500);
    }
  }
  
  public function getFilterByCategory($categoryId){
    try{
      $result = $this->brandService->getFilterByCategory($categoryId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las marcas', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->brandService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de la marca', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->brandValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->brandService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear la marca', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->brandValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->brandService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la marca', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos de la marca', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->brandService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar la marca', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->brandService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar la marca', 'error' => $e->getMessage()], 500);
    }
    
  }
}