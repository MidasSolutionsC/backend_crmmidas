<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\UsuarioService;
use App\Validator\UsuarioValidator;
use Illuminate\Support\Carbon;

class UsuarioController extends Controller{

  private $request;
  private $usuarioService;
  private $usuarioValidator;

  public function __construct(Request $request, UsuarioService $usuarioService, UsuarioValidator $usuarioValidator) {
    $this->request = $request;
    $this->usuarioService = $usuarioService;
    $this->usuarioValidator = $usuarioValidator;
  }

  public function login(){
    try{
      $validator = $this->usuarioValidator->validate('login');
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->usuarioService->login($this->request->all());
        $response = $this->response();
    
        if($result){
          $this->request->session()->regenerate();
          $response = $this->response(['login' => $result]);
        } else {
          $response = $this->response(['login' => $result, 'message' => 'Correo o clave incorrecta']);
        }
      }

      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al iniciar sesión', 'error' => $e->getMessage()], 500);
    }
  }

  public function listAll(){
    try{
      $result = $this->usuarioService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los usuarios', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->usuarioService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->usuarioValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $this->request->merge(['fecha_registro' => Carbon::now()]);
        $result = $this->usuarioService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->usuarioValidator->validate('update');
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->usuarioService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del usuario']);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try {
      $result = $this->usuarioService->delete($id);
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
      $result = $this->usuarioService->restore($id);
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