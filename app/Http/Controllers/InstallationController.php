<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Services\Implementation\InstallationService;
use App\Services\Implementation\SaleService;
use App\Validator\InstallationValidator;
use App\Validator\SaleValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstallationController extends Controller{

  private $request;
  private $installationService;
  private $installationValidator;

  private $saleService;
  private $saleValidator;

  public function __construct(
    Request $request, 
    InstallationService $installationService, 
    InstallationValidator $installationValidator,
    SaleService $saleService,
    SaleValidator $saleValidator
  )
  {
    $this->request = $request;
    $this->installationService = $installationService;
    $this->installationValidator = $installationValidator;
    $this->saleService = $saleService;
    $this->saleValidator = $saleValidator;
  }

  public function listAll(){
    try{
      $result = $this->installationService->getAll();
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
      $result = $this->installationService->search($this->request->all());
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
      $result = $this->installationService->getBySale($saleId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de la instalación', 'error' => $e->getMessage()], 500);
    }
  }

  public function getByAddress($addressId){
    try{
      $result = $this->installationService->getByAddress($addressId);
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
      $result = $this->installationService->getById($id);
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
      $validator = $this->installationValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->installationService->create($this->request->all());
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
        // Consulta el último registro
        $latestSale = Sale::latest()->first();
        $nro_orden = 1;
        if($latestSale){
          $nro_orden = $latestSale->nro_orden + 1;
        }

        $reqSale = [
          "nro_orden" => $nro_orden,
          "comentario" => "",
          "user_create_id" => $this->request->input('user_auth_id')
        ];

        $resSale = $this->saleService->create($reqSale);
        if($resSale){
          // $ventasId = $resSale->id;
          $this->request['ventas_id'] = $resSale->id;
        }
      } 


      $validator = $this->installationValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->installationService->create($this->request->all());
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
      $validator = $this->installationValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->installationService->update($this->request->all(), $id);
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

  public function updateComplete($id){
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      $ventasId = $this->request->input('ventas_id');

      if(empty($ventasId)){
        // Consulta el último registro
        $latestSale = Sale::latest()->first();
        $nro_orden = 1;
        if($latestSale){
          $nro_orden = $latestSale->nro_orden + 1;
        }

        $reqSale = [
          "nro_orden" => $nro_orden,
          "comentario" => "",
          "user_create_id" => $this->request->input('user_auth_id')
        ];

        $resSale = $this->saleService->create($reqSale);
        if($resSale){
          // $ventasId = $resSale->id;
          $this->request['ventas_id'] = $resSale->id;
        }
      } 

      $this->installationValidator->setRequest($this->request->all(), $id);
      $validator = $this->installationValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {

        // $response = $this->responseUpdate([$this->request->all()]);
        $result = $this->installationService->update($this->request->all(), $id);
        $response = $this->responseUpdate([$result]);
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
      return $this->responseError(['message' => 'Error al actualizar la instalación', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->installationService->delete($id);
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
      $result = $this->installationService->restore($id);
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