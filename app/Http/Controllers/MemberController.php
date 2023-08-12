<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\MemberService;
use App\Validator\MemberValidator;

class MemberController extends Controller{
  private $request;
  private $memberService;
  private $memberValidator;

  public function __construct(Request $request, MemberService $memberService, MemberValidator $memberValidator)
  {
    $this->request = $request;
    $this->memberService = $memberService;
    $this->memberValidator = $memberValidator;
    
  }

  public function listAll(){
    try{
      $result = $this->memberService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los integrantes', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->memberService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del integrante', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->memberValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->memberService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el integrante', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->memberValidator->validate('update');
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->memberService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del integrante', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del integrante', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->memberService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el integrante', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->memberService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el integrante', 'error' => $e->getMessage()], 500);
    }
    
  }
}