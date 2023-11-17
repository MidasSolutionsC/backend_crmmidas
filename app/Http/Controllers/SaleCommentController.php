<?php
namespace App\Http\Controllers;

use App\Services\Implementation\SaleCommentService;
use App\Validator\SaleCommentValidator;
use Illuminate\Http\Request;

class SaleCommentController extends Controller{

  private $request;
  private $saleCommentService;
  private $saleCommentValidator;

  public function __construct(Request $request, SaleCommentService $saleCommentService, SaleCommentValidator $saleCommentValidator)
  {
    $this->request = $request;
    $this->saleCommentService = $saleCommentService;
    $this->saleCommentValidator = $saleCommentValidator;
  }

  public function listAll(){
    try{
      $result = $this->saleCommentService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los comentarios', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterBySale($saleId){
    try{
      $result = $this->saleCommentService->getFilterBySale($saleId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los comentarios de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->saleCommentService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del comentario', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->saleCommentValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleCommentService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el comentario', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->saleCommentValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleCommentService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del comentario', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del comentario', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->saleCommentService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el comentario', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->saleCommentService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el comentario', 'error' => $e->getMessage()], 500);
    }
    
  }
}