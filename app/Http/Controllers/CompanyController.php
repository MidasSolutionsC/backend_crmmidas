<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\CompanyService;
use App\Validator\CompanyValidator;

class CompanyController extends Controller{

  private $request;
  private $companyService;
  private $companyValidator;

  public function __construct(Request $request, CompanyService $companyService, CompanyValidator $companyValidator)
  {
    $this->request = $request;
    $this->companyService = $companyService;
    $this->companyValidator = $companyValidator;
  }
  
  public function index(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->companyService->index($data);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las empresas', 'error' => $e->getMessage()], 500);
    }
  }

  public function listAll(){
    try{
      $result = $this->companyService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las empresas', 'error' => $e->getMessage()], 500);
    }
  }

  public function search(){
    try{
      $result = $this->companyService->search($this->request->all());
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las empresas', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->companyService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de la empresa', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->companyValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->companyService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear la empresa', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->companyValidator->validate();  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->companyService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la empresa', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos de la empresa', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->companyService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar la empresa', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->companyService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar la empresa', 'error' => $e->getMessage()], 500);
    }
    
  }

}