<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\UserService;
use App\Validator\UserValidator;

class UserController extends Controller{

  private $request;
  private $userService;
  private $userValidator;

  public function __construct(Request $request, UserService $userService, UserValidator $userValidator) {
    $this->request = $request;
    $this->userService = $userService;
    $this->userValidator = $userValidator;
  }

  public function listAll(){
    try{
      $result = $this->userService->getAll();
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los usuarios', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->userService->getById($id);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->userValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->userService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->userValidator->validate('update');
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->userService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del usuario del usuario', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try {
      $result = $this->userService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try {
      $result = $this->userService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }

      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el usuario', 'error' => $e->getMessage()], 500);
    }
  }
}