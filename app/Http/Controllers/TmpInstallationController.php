<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\TmpInstallationService;
use App\Services\Implementation\TmpSaleService;
use App\Validator\TmpInstallationValidator;
use App\Validator\TmpSaleValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use function PHPUnit\Framework\isEmpty;

class TmpInstallationController extends Controller{

  private $request;
  private $tmpInstallationService;
  private $tmpInstallationValidator;

  private $tmpSaleService;
  private $tmpSaleValidator;


  public function __construct(
    Request $request, 
    TmpInstallationService $tmpInstallationService, 
    TmpInstallationValidator $tmpInstallationValidator,
    TmpSaleService $tmpSaleService,
    TmpSaleValidator $tmpSaleValidator,
    )
  {
    $this->request = $request;
    $this->tmpInstallationService = $tmpInstallationService;
    $this->tmpInstallationValidator = $tmpInstallationValidator;
    $this->tmpSaleService = $tmpSaleService;
    $this->tmpSaleValidator = $tmpSaleValidator;
  }

  public function listAll(){
    try{
      $result = $this->tmpInstallationService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las instalaciones', 'error' => $e->getMessage()], 500);
    }
  }

  public function search(){
    try{
      $result = $this->tmpInstallationService->search($this->request->all());
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de la instalación', 'error' => $e->getMessage()], 500);
    }
  }

  public function getBySale($saleId){
    try{
      $result = $this->tmpInstallationService->getBySale($saleId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de la instalación', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->tmpInstallationService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de la instalación', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->tmpInstallationValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->tmpInstallationService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear la instalación', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete(){
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      $ventasId = $this->request->input('ventas_id');

      if(empty($ventasId)){
        $reqSale = [
          "comentario" => "pendiente",
          "user_create_id" => $this->request->input('user_auth_id')
        ];

        $resSale = $this->tmpSaleService->create($reqSale);
        if($resSale){
          // $ventasId = $resSale->id;
          $this->request['ventas_id'] = $resSale->id;
        }
      } 


      $validator = $this->tmpInstallationValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->tmpInstallationService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      // Si todo está bien, confirmar la transacción
      DB::commit();
      return $response;
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch(\Exception $e){
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al crear la instalación', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->tmpInstallationValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->tmpInstallationService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la instalación', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos de la instalación', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->tmpInstallationService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar la instalación', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->tmpInstallationService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar la instalación', 'error' => $e->getMessage()], 500);
    }
    
  }
}