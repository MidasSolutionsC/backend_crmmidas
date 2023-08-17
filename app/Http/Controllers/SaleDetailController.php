<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\SaleDetailService;
use App\Validator\SaleDetailValidator;

class SaleDetailController extends Controller{

  private $request;
  private $saleDetailService;
  private $saleDetailValidator;

  public function __construct(Request $request, SaleDetailService $saleDetailService, SaleDetailValidator $saleDetailValidator)
  {
    $this->request = $request;
    $this->saleDetailService = $saleDetailService;
    $this->saleDetailValidator = $saleDetailValidator;
  }

  public function listAll(){
    try{
      $result = $this->saleDetailService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los detalles de ventas', 'error' => $e->getMessage()], 500);
    }
  }
  
  public function getFilterBySale($saleId){
    try{
      $result = $this->saleDetailService->getFilterBySale($saleId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los detalles de ventas', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->saleDetailService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del detalles de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->saleDetailValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleDetailService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el detalle de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->saleDetailValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleDetailService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del detalle de la venta', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del detalle de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->saleDetailService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el detalle de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->saleDetailService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el detalle de la venta', 'error' => $e->getMessage()], 500);
    }
    
  }
}