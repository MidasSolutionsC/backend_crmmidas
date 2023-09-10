<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\CategoryBrandService;
use App\Validator\CategoryBrandValidator;

class CategoryBrandController extends Controller{
  private $request;
  private $categoryBrandService;
  private $categoryBrandValidator;


  public function __construct(Request $request, CategoryBrandService $categoryBrandService, CategoryBrandValidator $categoryBrandValidator)
  {
    $this->request = $request;
    $this->categoryBrandService = $categoryBrandService;
    $this->categoryBrandValidator = $categoryBrandValidator;
  }

  public function index(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->categoryBrandService->index($data);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las categorías', 'error' => $e->getMessage()], 500);
    }
  }

  public function listAll(){
    try{
      $result = $this->categoryBrandService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las categorías', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->categoryBrandService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de la categoría', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->categoryBrandValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->categoryBrandService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear la categoría', 'error' => $e->getMessage()], 500);
    }
  }
  
  public function update($id){
    try{
      $validator = $this->categoryBrandValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->categoryBrandService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la categoría', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos de la categoría', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->categoryBrandService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar la categoría', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->categoryBrandService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar la categoría', 'error' => $e->getMessage()], 500);
    }
    
  }
}