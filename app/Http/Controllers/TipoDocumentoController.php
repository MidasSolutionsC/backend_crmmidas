<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoDocumento;
use App\Services\Implementation\TipoDocumentoService;
use App\Validator\TipoDocumentoValidator;
use Illuminate\Support\Carbon;

class TipoDocumentoController extends Controller{
  
  private $request;
  private $tipoDocumentoService;
  private $tipoDocumentoValidator;

  public function __construct(Request $request, TipoDocumentoService $tipoDocumentoService, TipoDocumentoValidator $tipoDocumentoValidator) {
    $this->request = $request;
    $this->tipoDocumentoService = $tipoDocumentoService;
    $this->tipoDocumentoValidator = $tipoDocumentoValidator;
  }

  public function listAll(){
    try{
      $result = $this->tipoDocumentoService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los tipos de documentos', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->tipoDocumentoService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del tipo de documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->tipoDocumentoValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        // $this->request->merge(['fecha_registro' => Carbon::now()]);
        $result = $this->tipoDocumentoService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el tipo de documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->tipoDocumentoValidator->validate('update');
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->tipoDocumentoService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del tipo de documento']);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del tipo de documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->tipoDocumentoService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el tipo de documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->tipoDocumentoService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el tipo de documento', 'error' => $e->getMessage()], 500);
    }
    
  }
}