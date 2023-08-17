<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\BankAccountService;
use App\Validator\BankAccountValidator;

class BankAccountController extends Controller{

  private $request;
  private $bankAccountService;
  private $bankAccountValidator;

  public function __construct(Request $request, BankAccountService $bankAccountService, BankAccountValidator $bankAccountValidator)
  {
    $this->request = $request;
    $this->bankAccountService = $bankAccountService;
    $this->bankAccountValidator = $bankAccountValidator;
  }

  public function listAll(){
    try{
      $result = $this->bankAccountService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las cuentas bancarias', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByClient(int $clientId){
    try{
      $result = $this->bankAccountService->getFilterByClient($clientId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las cuentas bancarias', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->bankAccountService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de la cuenta bancaria', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->bankAccountValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->bankAccountService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear la cuenta bancaria', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->bankAccountValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->bankAccountService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la cuenta bancaria', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos de la cuenta bancaria', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->bankAccountService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar la cuenta bancaria', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->bankAccountService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar la cuenta bancaria', 'error' => $e->getMessage()], 500);
    }
    
  }
}