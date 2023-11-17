<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\ManualService;
use App\Validator\ManualValidator;
use App\Utilities\FileUploader;

class ManualController extends Controller{

  private $request;
  private $manualService;
  private $manualValidator;

  public function __construct(Request $request, ManualService $manualService, ManualValidator $manualValidator)
  {
    $this->request = $request;
    $this->manualService = $manualService;
    $this->manualValidator = $manualValidator;
  }
  
  public function index(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->manualService->index($data);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los manuales', 'error' => $e->getMessage()], 500);
    }
  }
  
  public function listAll(){
    try{
      $result = $this->manualService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los manuales', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->manualService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del manual', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->manualValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        
        if($this->request->has('file')){
          $file = $this->request->file('file');
          $fileName = FileUploader::upload($file, 'files/manual/', []);
          $this->request['archivo'] = $fileName;
        }
                
        $result = $this->manualService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el manual', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->manualValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        if($this->request->has('file')){
          $file = $this->request->file('file');
          $fileName = FileUploader::upload($file, 'files/manual/', []);
          $this->request['archivo'] = $fileName;
        }
        
        $result = $this->manualService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del manual', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del manual', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->manualService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el manual', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->manualService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el manual', 'error' => $e->getMessage()], 500);
    }
    
  }
}