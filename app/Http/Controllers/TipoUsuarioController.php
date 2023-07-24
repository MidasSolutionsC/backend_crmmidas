<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoUsuario;
use App\Services\Implementation\TipoUsuarioService;
use App\Validator\TipoUsuarioValidator;

class TipoUsuarioController extends Controller{

  /**
   * @var TipoUsuarioService
   */
  private $tipoUsuarioService;

  /**
   * @var TipoUsuarioValidator
   */
  private $tipoUsuarioValidator;
  
  /**
   * @var Request
   */
  private $request;


  public function __construct(TipoUsuarioService $tipoUsuarioService, Request $request, TipoUsuarioValidator $tipoUsuarioValidator)
  {
    $this->request = $request;
    $this->tipoUsuarioService = $tipoUsuarioService;
    $this->tipoUsuarioValidator = $tipoUsuarioValidator;
  }

  public function listAll(){
    $result = $this->tipoUsuarioService->getAll();
    $response = $this->response();

    if($result != null){
      $response = $this->response($result);
    } 

    return $response;
  }

  public function get($id){
    $result = $this->tipoUsuarioService->getById($id);
    $response = $this->response();

    if($result != null){
      $response = $this->response([$result]);
    } 

    return $response;
  }

  public function create(){
    $validator = $this->tipoUsuarioValidator->validate();

    if($validator->fails()){
      $response = $this->responseError($validator->errors(), 422);
    } else {
      $result = $this->tipoUsuarioService->create($this->request->all());
      $response = $this->responseCreated([$result]);
    }

    return $response;
  }

  public function update($id){
    $validator = $this->tipoUsuarioValidator->validate();

    if($validator->fails()){
      $response = $this->responseError($validator->errors(), 422);
    } else {
      $result = $this->tipoUsuarioService->update($this->request->all(), $id);
      $response = $this->responseUpdate([$result]);
    }

    return $response;
  }

  public function delete($id){
    $result = $this->tipoUsuarioService->delete($id);
    if($result){
      $response = $this->responseDelete([$result]);
    } else {
      $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
    }

    return $response;
  }

  public function restore($id){
    $result = $this->tipoUsuarioService->restore($id);
    if($result){
      $response = $this->responseRestore([$result]);
    } else {
      $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
    }

    return $response;
  }
}