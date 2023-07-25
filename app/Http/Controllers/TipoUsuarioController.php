<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\TipoUsuarioService;
use App\Validator\TipoUsuarioValidator;
use Illuminate\Support\Carbon;

class TipoUsuarioController extends Controller{

  /**
 * @var Request
 */

  private $request;
  /**
   * @var TipoUsuarioService
   */
  private $tipoUsuarioService;

  /**
   * @var TipoUsuarioValidator
   */
  private $tipoUsuarioValidator;
  
  public function __construct(Request $request, TipoUsuarioService $tipoUsuarioService, TipoUsuarioValidator $tipoUsuarioValidator)
  {
    $this->request = $request;
    $this->tipoUsuarioService = $tipoUsuarioService;
    $this->tipoUsuarioValidator = $tipoUsuarioValidator;
  }

  public function listAll(){
    try{
      $result = $this->tipoUsuarioService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los tipo de usuarios', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->tipoUsuarioService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del tipo de usuario', 'error' => $e->getMessage()], 500);
    }
    
  }

  public function create(){
    try{
      $validator = $this->tipoUsuarioValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $this->request->merge(['fecha_registro' => Carbon::now()]);
        $result = $this->tipoUsuarioService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el tipo de usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try {
      $validator = $this->tipoUsuarioValidator->validate('update');
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->tipoUsuarioService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del tipo de usuario']);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del tipo de usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try {
      $result = $this->tipoUsuarioService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el tipo de usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try {
      $result = $this->tipoUsuarioService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el tipo de usuario', 'error' => $e->getMessage()], 500);
    }
  }
}